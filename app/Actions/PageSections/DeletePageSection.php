<?php

namespace App\Actions\PageSections;

use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Support\Facades\DB;

class DeletePageSection
{
    public function handle(Page $page, PageSection $section): void
    {
        DB::transaction(function () use ($page, $section): void {
            $owner = Page::query()->whereKey($page->id)->lockForUpdate()->firstOrFail();
            $owned = $owner->sections()->whereKey($section->id)->lockForUpdate()->firstOrFail();
            if (! $owned->delete()) {
                throw new \RuntimeException('Section deletion failed.');
            }
            PageSection::normalizeOrdering($owner);
        });
    }
}
