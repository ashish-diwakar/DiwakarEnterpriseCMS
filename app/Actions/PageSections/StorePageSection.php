<?php

namespace App\Actions\PageSections;

use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Support\Facades\DB;

class StorePageSection
{
    public function handle(Page $page, array $attributes): PageSection
    {
        return DB::transaction(function () use ($page, $attributes): PageSection {
            $owner = Page::query()->whereKey($page->id)->lockForUpdate()->firstOrFail();
            PageSection::lockImageReference($attributes['media_id'] ?? null);
            $sections = PageSection::normalizeOrdering($owner);
            $section = new PageSection($attributes);
            $section->forceFill(['page_id' => $owner->id, 'sort_order' => $sections->count() + 1])->saveOrFail();

            return $section;
        });
    }
}
