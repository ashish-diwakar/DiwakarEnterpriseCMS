<?php

namespace App\Support;

use App\Enums\MenuItemType;
use App\Enums\PageStatus;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use App\Rules\SafeMenuUrl;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class MenuResolver
{
    public function forLocation(string $location): array
    {
        return $this->resolve('location', $location);
    }

    public function forSlug(string $slug): array
    {
        return $this->resolve('slug', $slug);
    }

    private function resolve(string $field, string $value): array
    {
        // Graceful pre-install behavior; never seed or fall back to hardcoded links.
        if (! Schema::hasTable('menus')) {
            return [];
        }
        $menu = Menu::query()->where($field, $value)->where('is_active', true)->with('items.page')->first();

        return $menu ? $this->inspect($menu)['nodes'] : [];
    }

    /** Assess persisted state without probing destinations or changing records. */
    public function inspect(Menu $menu): array
    {
        $menu->loadMissing('items.page');
        $visibility = [];
        foreach ($menu->items as $item) {
            $visibility[$item->id] = ['visible' => false, 'reasons' => ['Invalid or unavailable menu hierarchy.']];
        }
        $nodes = $this->branch($menu->items, null, $visibility);
        // Parent-first propagation uses the same bounded hierarchy as administration.
        foreach (MenuTree::flatten($menu->items) as $row) {
            $item = $row['item'];
            $reasons = $visibility[$item->id]['reasons'];
            if (! $menu->is_active) {
                array_unshift($reasons, 'The parent menu is inactive.');
            }
            if ($item->parent_id !== null && ! $visibility[$item->parent_id]['visible']) {
                $reasons[] = 'A parent item is hidden from public navigation.';
            }
            $visibility[$item->id] = ['visible' => $reasons === [], 'reasons' => $reasons];
        }

        return ['nodes' => $menu->is_active ? $nodes : [], 'items' => $visibility];
    }

    private function branch(Collection $items, ?int $parentId, array &$visibility, int $depth = 1, array $seen = []): array
    {
        if ($depth > MenuTree::MAX_DEPTH) {
            return [];
        }
        $nodes = [];
        foreach ($items->where('parent_id', $parentId) as $item) {
            if (isset($seen[$item->id])) {
                continue;
            }
            [$url, $reasons] = $this->destination($item);
            $children = $this->branch($items, $item->id, $visibility, $depth + 1, $seen + [$item->id => true]);
            if ($item->type === MenuItemType::Group && $children === []) {
                $reasons[] = 'This navigation group has no publicly eligible children.';
            }
            $visibility[$item->id] = ['visible' => $reasons === [], 'reasons' => $reasons];
            if ($reasons !== []) {
                continue;
            }
            $nodes[] = ['label' => $item->label, 'url' => $url, 'target' => $item->target === '_blank' ? '_blank' : '_self', 'children' => $children];
        }

        return $nodes;
    }

    private function destination(MenuItem $item): array
    {
        $reasons = $item->is_active ? [] : ['This menu item is inactive.'];
        $url = null;
        if ($item->type === MenuItemType::Page) {
            if ($item->page === null) {
                $reasons[] = 'The associated Page is unavailable.';
            } elseif ($item->page->status !== PageStatus::Published) {
                $reasons[] = 'The associated Page is unpublished.';
            } else {
                $url = $this->pageUrl($item->page);
                if ($url === null) {
                    $reasons[] = 'Public Page routing is unavailable.';
                } elseif (! SafeMenuUrl::accepts($url)) {
                    $reasons[] = 'The destination is invalid or unavailable.';
                }
            }
        } elseif ($item->type === MenuItemType::Url) {
            $url = $item->url;
            if (! SafeMenuUrl::accepts($url)) {
                $reasons[] = 'The destination is invalid or unavailable.';
            }
        }

        return [$url, $reasons];
    }

    protected function pageUrl(Page $page): ?string
    {
        // F007/F008 deliberately defer public Page routing. No route is fabricated.
        return null;
    }
}
