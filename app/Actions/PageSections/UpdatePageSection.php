<?php

namespace App\Actions\PageSections;

use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Support\Facades\DB;

class UpdatePageSection
{
    public function handle(Page $page, PageSection $section, array $attributes): void
    {
        DB::transaction(function () use ($page, $section, $attributes): void {
            $owner = Page::query()->whereKey($page->id)->lockForUpdate()->firstOrFail();
            $owned = $owner->sections()->whereKey($section->id)->lockForUpdate()->firstOrFail();
            PageSection::lockImageReference(array_key_exists('media_id', $attributes) ? $attributes['media_id'] : $owned->media_id);
            $owned->fill($attributes)->saveOrFail();
        });
    }
}
