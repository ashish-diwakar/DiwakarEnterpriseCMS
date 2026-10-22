<x-admin-layout title="Page Sections" description="Arrange structured content. Public rendering remains deferred.">
    <div class="space-y-6" x-data="{ deleteHeading: '', deleteAction: '', deleting: false }">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="min-w-0"><h2 class="break-words text-lg font-semibold">{{ $page->title }}</h2><p class="text-sm text-slate-500">Page status: {{ $page->status->label() }}</p></div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.pages.edit', $page) }}" class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm">Edit Page identity</a>
                <a href="{{ route('admin.pages.index') }}" class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm">Pages</a>
                <a href="{{ route('admin.pages.sections.create', $page) }}" class="rounded-md bg-slate-950 px-4 py-2 text-sm font-medium text-white">Add Section</a>
            </div>
        </div>
        @forelse ($sections as $section)
            <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-4 sm:flex-row">
                    @if ($section->media)
                        <img src="{{ $section->media->url() }}" alt="" loading="lazy" class="h-24 w-full object-contain sm:w-32">
                    @endif
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-medium text-slate-500">{{ $section->sort_order }} · {{ $section->type->label() }} · {{ $section->is_enabled ? 'Enabled' : 'Disabled' }}</p>
                        <h3 class="mt-2 break-words font-semibold">{{ $section->heading }}</h3>
                        <p class="mt-2 break-words text-sm text-slate-600">{{ \Illuminate\Support\Str::limit($section->body ?? '', 180) }}</p>
                    </div>
                </div>
                <div class="mt-5 flex flex-wrap items-center gap-3 border-t border-slate-100 pt-4">
                    @foreach (['up' => 'Move Up', 'down' => 'Move Down'] as $direction => $label)
                        <form method="POST" action="{{ route('admin.pages.sections.move', [$page, $section]) }}">
                            @csrf @method('PATCH')
                            <input type="hidden" name="direction" value="{{ $direction }}">
                            <button @disabled(($direction === 'up' && $loop->parent->first) || ($direction === 'down' && $loop->parent->last)) class="rounded-md border border-slate-300 px-3 py-2 text-sm disabled:opacity-40">{{ $label }}<span class="sr-only"> {{ $section->heading }}</span></button>
                        </form>
                    @endforeach
                    <form method="POST" action="{{ route('admin.pages.sections.visibility', [$page, $section]) }}">
                        @csrf @method('PATCH')
                        <input type="hidden" name="is_enabled" value="{{ $section->is_enabled ? '0' : '1' }}">
                        <button class="rounded-md border border-slate-300 px-3 py-2 text-sm">{{ $section->is_enabled ? 'Disable' : 'Enable' }}<span class="sr-only"> {{ $section->heading }}</span></button>
                    </form>
                    <a href="{{ route('admin.pages.sections.edit', [$page, $section]) }}" class="rounded-md border border-slate-300 px-3 py-2 text-sm">Edit<span class="sr-only"> {{ $section->heading }}</span></a>
                    <button type="button" data-heading="{{ $section->heading }}" data-action="{{ route('admin.pages.sections.destroy', [$page, $section]) }}" x-on:click="deleteHeading = $el.dataset.heading; deleteAction = $el.dataset.action; $refs.deleteDialog.showModal()" class="rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">Delete<span class="sr-only"> {{ $section->heading }}</span></button>
                </div>
            </article>
        @empty
            <p class="rounded-lg border border-slate-200 bg-white p-6 text-sm text-slate-600">This Page has no sections. Add a section to begin.</p>
        @endforelse
        <dialog x-ref="deleteDialog" aria-labelledby="delete-section-heading" aria-describedby="delete-section-message" x-on:cancel="if (deleting) $event.preventDefault()" x-on:close="deleting = false" class="m-auto max-w-lg overflow-y-auto rounded-xl bg-white p-6 shadow-xl backdrop:bg-slate-950/60" style="width: calc(100% - 2rem); max-height: calc(100% - 2rem);">
            <h2 id="delete-section-heading" class="text-lg font-semibold">Delete Section?</h2>
            <p id="delete-section-message" class="mt-3 break-words text-sm text-slate-600">Permanently delete "<span x-text="deleteHeading"></span>"? This cannot be undone. Associated Media files will remain in the library.</p>
            <form method="POST" x-bind:action="deleteAction" x-on:submit="if (deleting) { $event.preventDefault() } else { deleting = true }" class="flex flex-wrap justify-end gap-3 border-t border-slate-200" style="margin-top: 1.5rem; padding-top: 1.25rem;">
                @csrf @method('DELETE')
                <button type="button" autofocus x-bind:disabled="deleting" x-on:click="$refs.deleteDialog.close()" class="rounded-md border border-slate-300 px-4 py-2 text-sm">Cancel</button>
                <button x-bind:disabled="deleting || !deleteAction" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white">Delete Section</button>
            </form>
        </dialog>
    </div>
</x-admin-layout>
