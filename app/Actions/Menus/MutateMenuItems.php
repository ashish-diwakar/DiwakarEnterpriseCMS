<?php

namespace App\Actions\Menus;

use App\Enums\MenuItemType;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use App\Support\MenuTree;
use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MutateMenuItems
{
    public function save(Menu $menu, ?MenuItem $item, array $data): MenuItem
    {
        return DB::transaction(function () use ($menu, $item, $data): MenuItem {
            // Same Page-first lock order as Page deletion and F008 references.
            if ($data['type'] === MenuItemType::Page->value && ! Page::query()->whereKey($data['page_id'])->lockForUpdate()->first()) {
                throw ValidationException::withMessages(['page_id' => 'The selected Page is no longer available.']);
            }

            return $this->mutate($menu, $data['version'], function (Menu $owner, Collection $items) use ($item, $data): MenuItem {
                $record = $item ? $items->firstWhere('id', $item->id) : new MenuItem;
                abort_if($record === null, 404);
                $parentId = isset($data['parent_id']) ? (int) $data['parent_id'] : null;
                MenuTree::validate($items, $record->id ?? 0, $parentId);
                $parentChanged = ! $record->exists || $record->parent_id !== $parentId;
                $record->fill([
                    'label' => $data['label'], 'type' => $data['type'], 'is_active' => $data['is_active'],
                    'page_id' => $data['type'] === 'page' ? $data['page_id'] : null,
                    'url' => $data['type'] === 'url' ? $data['url'] : null,
                    'target' => $data['type'] === 'group' ? '_self' : $data['target'],
                ])->forceFill(['menu_id' => $owner->id, 'parent_id' => $parentId]);
                if ($parentChanged) {
                    $record->sort_order = (int) $items->where('parent_id', $parentId)->max('sort_order') + 1;
                }
                $record->saveOrFail();

                return $record;
            });
        });
    }

    public function delete(Menu $menu, MenuItem $item, int $version): void
    {
        $this->mutate($menu, $version, function (Menu $owner, Collection $items) use ($item): void {
            $record = $items->firstWhere('id', $item->id);
            abort_if($record === null, 404);
            if ($items->where('parent_id', $record->id)->isNotEmpty()) {
                throw ValidationException::withMessages(['items' => 'Remove the child items before deleting this parent item.']);
            }
            $record->deleteOrFail();
        });
    }

    public function move(Menu $menu, MenuItem $item, int $version, string $direction): void
    {
        $this->mutate($menu, $version, function (Menu $owner, Collection $items) use ($item, $direction): void {
            $record = $items->firstWhere('id', $item->id);
            abort_if($record === null, 404);
            $siblings = $items->where('parent_id', $record->parent_id)->values();
            $index = $siblings->search(fn ($node) => $node->id === $record->id);
            $neighbor = $index + ($direction === 'up' ? -1 : 1);
            if ($neighbor < 0 || $neighbor >= $siblings->count()) {
                return;
            }
            $order = $record->sort_order;
            $record->forceFill(['sort_order' => $siblings[$neighbor]->sort_order])->saveOrFail();
            $siblings[$neighbor]->forceFill(['sort_order' => $order])->saveOrFail();
        });
    }

    public function reorder(Menu $menu, ?int $parentId, array $ids, int $version): void
    {
        $this->mutate($menu, $version, function (Menu $owner, Collection $items) use ($parentId, $ids): void {
            if ($parentId !== null && ! $items->contains('id', $parentId)) {
                throw ValidationException::withMessages(['items' => 'The parent must belong to this menu.']);
            }
            $siblings = $items->where('parent_id', $parentId)->keyBy('id');
            $submitted = array_map(intval(...), $ids);
            $expected = $siblings->keys()->all();
            $sorted = $submitted;
            sort($expected);
            sort($sorted);
            if ($sorted !== $expected) {
                throw ValidationException::withMessages(['items' => 'Submit each item in this sibling group exactly once.']);
            }
            foreach ($submitted as $index => $id) {
                $siblings[$id]->forceFill(['sort_order' => $index + 1])->saveOrFail();
            }
        });
    }

    private function mutate(Menu $menu, int $version, Closure $operation): mixed
    {
        return DB::transaction(function () use ($menu, $version, $operation) {
            $owner = Menu::query()->whereKey($menu->id)->lockForUpdate()->firstOrFail();
            if ($owner->lock_version !== $version) {
                throw ValidationException::withMessages(['items' => 'This menu changed in another request. Reload the menu before trying again.']);
            }
            $items = $owner->items()->lockForUpdate()->get();
            $result = $operation($owner, $items);
            $positions = [];
            foreach ($owner->items()->get() as $item) {
                $group = $item->parent_id ?? 'root';
                $position = $positions[$group] = ($positions[$group] ?? 0) + 1;
                if ($item->sort_order !== $position) {
                    $item->forceFill(['sort_order' => $position])->saveOrFail();
                }
            }
            $owner->forceFill(['lock_version' => $owner->lock_version + 1])->saveOrFail();

            return $result;
        });
    }
}
