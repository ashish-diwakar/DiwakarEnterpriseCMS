<x-admin-layout title="Pages" description="Manage page identity and publication status. Content and public rendering are deferred.">
    <div class="space-y-6" x-data="{ deleteTitle: '', deleteAction: '', deleting: false }">
        <div class="flex justify-end">
            <a href="{{ route('admin.pages.create') }}" class="inline-flex items-center justify-center rounded-md bg-slate-950 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2">Create page</a>
        </div>
        <form method="GET" action="{{ route('admin.pages.index') }}" class="flex flex-col gap-4 rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:flex-row sm:items-end">
            <div class="min-w-0 flex-1">
                <label for="search" class="block text-sm font-medium text-slate-700">Search title or slug</label>
                <input id="search" name="search" type="search" maxlength="200" value="{{ $filters['search'] ?? '' }}" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-slate-500 focus:ring-slate-500">
                @error('search') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-slate-700">Status</label>
                <select id="status" name="status" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-slate-500 focus:ring-slate-500">
                    @foreach (['all' => 'All', 'draft' => 'Draft', 'published' => 'Published'] as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['status'] ?? 'all') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('status') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="flex flex-wrap gap-3">
                <button class="rounded-md bg-slate-950 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2">Apply</button>
                <a href="{{ route('admin.pages.index') }}" class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2">Reset</a>
            </div>
        </form>
        <div class="md:overflow-hidden md:rounded-lg md:border md:border-slate-200 md:bg-white md:shadow-sm">
            <table class="block w-full table-fixed md:table">
                <thead class="hidden bg-slate-50 md:table-header-group">
                    <tr>
                        @foreach (['Title', 'Slug', 'Status', 'Updated', 'Actions'] as $heading)
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $heading }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="flex flex-col gap-4 md:table-row-group md:divide-y md:divide-slate-200">
                    @forelse ($pages as $page)
                        <tr class="block rounded-lg border border-slate-200 bg-white p-4 shadow-sm md:table-row md:rounded-none md:border-0 md:p-0 md:shadow-none">
                            <td class="block break-words py-2 text-sm font-medium text-slate-950 md:table-cell md:px-4 md:py-4"><span class="mr-2 text-xs text-slate-500 md:hidden">Title:</span>{{ $page->title }}</td>
                            <td class="block break-all py-2 text-sm text-slate-600 md:table-cell md:px-4 md:py-4"><span class="mr-2 text-xs text-slate-500 md:hidden">Slug:</span>{{ $page->slug }}</td>
                            <td class="block py-2 md:table-cell md:px-4 md:py-4"><span class="mr-2 text-xs text-slate-500 md:hidden">Status:</span><span @class(['inline-flex rounded-full px-2 py-1 text-xs font-medium', 'bg-emerald-50 text-emerald-800' => $page->status === \App\Enums\PageStatus::Published, 'bg-slate-100 text-slate-700' => $page->status === \App\Enums\PageStatus::Draft])>{{ $page->status->label() }}</span></td>
                            <td class="block py-2 text-xs text-slate-600 md:table-cell md:px-4 md:py-4"><span class="mr-2 text-slate-500 md:hidden">Updated:</span>{{ $page->updated_at->format('d M Y, H:i') }}</td>
                            <td class="block py-2 md:table-cell md:px-4 md:py-4">
                                <div class="flex flex-wrap items-center gap-2">
                                    <a href="{{ route('admin.pages.sections.index', $page) }}" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Manage Sections</a>
                                    <a href="{{ route('admin.pages.edit', $page) }}" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2">Edit<span class="sr-only"> {{ $page->title }}</span></a>
                                    <button type="button" data-page-title="{{ $page->title }}" data-delete-action="{{ route('admin.pages.destroy', $page) }}"
                                            x-on:click="deleteTitle = $el.dataset.pageTitle; deleteAction = $el.dataset.deleteAction; $refs.deleteDialog.showModal()"
                                            class="rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm font-medium text-red-700 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">Delete<span class="sr-only"> {{ $page->title }}</span></button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="block md:table-row"><td colspan="5" class="block p-6 text-sm text-slate-600 md:table-cell">No pages found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $pages->links() }}
        <dialog x-ref="deleteDialog" aria-labelledby="delete-page-heading" aria-describedby="delete-page-message"
                x-on:cancel="if (deleting) $event.preventDefault()" x-on:close="deleting = false"
                class="m-auto max-w-lg overflow-y-auto rounded-xl border-0 bg-white p-6 text-slate-900 shadow-xl backdrop:bg-slate-950/60"
                style="width: calc(100% - 2rem); max-height: calc(100% - 2rem);">
            <h2 id="delete-page-heading" class="text-lg font-semibold">Delete Page?</h2>
            <p id="delete-page-message" class="mt-3 break-words text-sm leading-6 text-slate-600">Are you sure you want to permanently delete "<span x-text="deleteTitle"></span>"? This action cannot be undone.</p>
            <form method="POST" x-bind:action="deleteAction" x-on:submit="if (deleting) { $event.preventDefault() } else { deleting = true }" x-bind:aria-busy="deleting" class="flex flex-wrap items-center justify-end gap-3 border-t border-slate-200" style="margin-top: 1.5rem; padding: 1.25rem 0.5rem 0.5rem; gap: 0.75rem;">
                @csrf
                @method('DELETE')
                <button type="button" autofocus x-bind:disabled="deleting" x-on:click="$refs.deleteDialog.close()" class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 disabled:opacity-50">Cancel</button>
                <button type="submit" x-bind:disabled="deleting || !deleteAction" x-text="deleting ? 'Deleting…' : 'Delete Page'" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-50">Delete Page</button>
            </form>
        </dialog>
    </div>
</x-admin-layout>
