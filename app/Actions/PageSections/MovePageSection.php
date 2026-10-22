<?php

namespace App\Actions\PageSections;

use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Support\Facades\DB;

class MovePageSection
{
    public function handle(Page $page, PageSection $section, string $direction): void
    {
        DB::transaction(function () use ($page, $section, $direction): void {
            $owner = Page::query()->whereKey($page->id)->lockForUpdate()->firstOrFail();
            $owner->sections()->whereKey($section->id)->firstOrFail();
            $sections = PageSection::normalizeOrdering($owner);
            $index = $sections->search(fn ($item) => $item->id === $section->id);
            $neighbor = $index + ($direction === 'up' ? -1 : 1);
            if ($neighbor < 0 || $neighbor >= $sections->count()) {
                return;
            }
            $sections[$index]->forceFill(['sort_order' => $neighbor + 1])->saveOrFail();
            $sections[$neighbor]->forceFill(['sort_order' => $index + 1])->saveOrFail();
        });
    }
}
