<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

final class MenuTree
{
    public const MAX_DEPTH = 3;

    /** Validate all affected nodes, including the moved item's descendants. */
    public static function validate(Collection $items, int $id, ?int $parentId): void
    {
        $parents = $items->mapWithKeys(fn ($item) => [$item->id => $item->parent_id])->all();
        if ($parentId !== null && ! array_key_exists($parentId, $parents)) {
            throw ValidationException::withMessages(['parent_id' => 'Select a parent from this menu.']);
        }
        $parents[$id] = $parentId;
        foreach (array_keys($parents) as $node) {
            $seen = [];
            $depth = 0;
            while ($node !== null) {
                if (isset($seen[$node]) || ++$depth > self::MAX_DEPTH || ! array_key_exists($node, $parents)) {
                    throw ValidationException::withMessages(['parent_id' => 'Menu items must form a tree with at most three levels; self-parenting and cycles are not allowed.']);
                }
                $seen[$node] = true;
                $node = $parents[$node];
            }
        }
    }

    public static function flatten(Collection $items, ?int $parentId = null, int $depth = 1, array $seen = []): array
    {
        if ($depth > self::MAX_DEPTH) {
            return [];
        }
        $rows = [];
        foreach ($items->where('parent_id', $parentId) as $item) {
            if (isset($seen[$item->id])) {
                continue;
            }
            $rows[] = ['item' => $item, 'depth' => $depth];
            $rows = array_merge($rows, self::flatten($items, $item->id, $depth + 1, $seen + [$item->id => true]));
        }

        return $rows;
    }
}
