<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PageStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePageRequest;
use App\Http\Requests\Admin\UpdatePageRequest;
use App\Models\Page;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use RuntimeException;

class PageController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:200'],
            'status' => ['nullable', Rule::in(['all', 'draft', 'published'])],
        ]);
        $query = Page::query();
        if (($filters['search'] ?? '') !== '') {
            $query->where(function ($query) use ($filters): void {
                $query->where('title', 'like', '%'.$filters['search'].'%')
                    ->orWhere('slug', 'like', '%'.$filters['search'].'%');
            });
        }
        if (in_array($filters['status'] ?? '', ['draft', 'published'], true)) {
            $query->where('status', $filters['status']);
        }

        return view('admin.pages.index', [
            'pages' => $query->orderByDesc('updated_at')->orderByDesc('id')->paginate(20)->appends($filters),
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        return view('admin.pages.create', ['page' => new Page, 'statuses' => PageStatus::cases()]);
    }

    public function store(StorePageRequest $request): RedirectResponse
    {
        $page = new Page;
        $this->persist($page, $request->validated());

        return redirect()->route('admin.pages.index')->with('status', 'Page created successfully.');
    }

    public function edit(Page $page): View
    {
        return view('admin.pages.edit', ['page' => $page, 'statuses' => PageStatus::cases()]);
    }

    public function update(UpdatePageRequest $request, Page $page): RedirectResponse
    {
        $this->persist($page, $request->validated());

        return redirect()->route('admin.pages.index')->with('status', 'Page updated successfully.');
    }

    public function destroy(Page $page): RedirectResponse
    {
        if (! $page->delete()) {
            throw new RuntimeException('Page deletion failed.');
        }

        return redirect()->route('admin.pages.index')->with('status', 'Page deleted successfully.');
    }

    private function persist(Page $page, array $attributes): void
    {
        try {
            $page->fill($attributes)->saveOrFail();
        } catch (UniqueConstraintViolationException $exception) {
            $conflict = Page::query()->where('slug', $page->slug);
            if ($page->exists) {
                $conflict->whereKeyNot($page->getKey());
            }
            if ($conflict->exists()) {
                throw ValidationException::withMessages(['slug' => 'This slug is already in use. Choose another slug.']);
            }
            throw $exception;
        }
    }
}
