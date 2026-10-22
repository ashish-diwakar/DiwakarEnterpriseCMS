<?php

namespace App\Http\Controllers\Admin;

use App\Actions\PageSections\DeletePageSection;
use App\Actions\PageSections\MovePageSection;
use App\Actions\PageSections\StorePageSection;
use App\Actions\PageSections\UpdatePageSection;
use App\Enums\PageSectionType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePageSectionRequest;
use App\Http\Requests\Admin\UpdatePageSectionRequest;
use App\Models\Media;
use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PageSectionController extends Controller
{
    public function index(Page $page): View
    {
        return view('admin.page-sections.index', ['page' => $page, 'sections' => $page->sections()->with('media')->get()]);
    }

    public function create(Request $request, Page $page): View
    {
        $request->validate(['type' => ['nullable', Rule::enum(PageSectionType::class)]]);
        $selected = old('type', $request->query('type', 'hero'));
        $type = PageSectionType::tryFrom(is_string($selected) ? $selected : '') ?? PageSectionType::Hero;

        return $this->form($page, new PageSection(['type' => $type, 'is_enabled' => true]), true);
    }

    public function store(StorePageSectionRequest $request, Page $page, StorePageSection $store): RedirectResponse
    {
        $store->handle($page, $request->validated());

        return redirect()->route('admin.pages.sections.index', $page)->with('status', 'Section created successfully.');
    }

    public function edit(Page $page, PageSection $section): View
    {
        return $this->form($page, $section, false);
    }

    private function form(Page $page, PageSection $section, bool $creating): View
    {
        $id = old('media_id', $section->media_id);
        $image = is_scalar($id) && $id !== '' ? Media::query()->whereKey($id)->whereIn('mime_type', PageSection::IMAGE_MIMES)->first() : null;

        return view('admin.page-sections.'.($creating ? 'create' : 'edit'), compact('page', 'section', 'creating', 'image'));
    }

    public function update(UpdatePageSectionRequest $request, Page $page, PageSection $section, UpdatePageSection $update): RedirectResponse
    {
        $update->handle($page, $section, $request->safe()->except('type'));

        return redirect()->route('admin.pages.sections.index', $page)->with('status', 'Section updated successfully.');
    }

    public function destroy(Page $page, PageSection $section, DeletePageSection $delete): RedirectResponse
    {
        $delete->handle($page, $section);

        return redirect()->route('admin.pages.sections.index', $page)->with('status', 'Section deleted successfully.');
    }

    public function move(Request $request, Page $page, PageSection $section, MovePageSection $move): RedirectResponse
    {
        $data = $request->validate(['direction' => ['required', Rule::in(['up', 'down'])]]);
        $move->handle($page, $section, $data['direction']);

        return redirect()->route('admin.pages.sections.index', $page)->with('status', 'Section order updated.');
    }

    public function visibility(Request $request, Page $page, PageSection $section, UpdatePageSection $update): RedirectResponse
    {
        $data = $request->validate(['is_enabled' => ['required', 'boolean']]);
        $update->handle($page, $section, $data);

        return redirect()->route('admin.pages.sections.index', $page)->with('status', 'Section visibility updated.');
    }
}
