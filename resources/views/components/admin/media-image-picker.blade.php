@props(['page', 'image' => null, 'required' => false])
@php
    $selectedId = old('media_id', $image?->id);
    $selectedId = is_scalar($selectedId) ? $selectedId : '';
@endphp
<div x-data="pageSectionImagePicker" data-endpoint="{{ route('admin.pages.sections.images', $page, absolute: false) }}"
     data-selected-id="{{ $selectedId }}" data-selected-url="{{ $image?->url() }}" data-selected-name="{{ $image ? ($image->title ?? $image->original_name) : '' }}" class="space-y-3">
    <p class="text-sm font-medium text-slate-700">Image{{ $required ? ' (required)' : ' (optional)' }}</p>
    <input type="hidden" name="media_id" value="{{ $selectedId }}" x-bind:value="selectedId">
    <div class="flex flex-col gap-3 rounded-md border border-slate-200 bg-slate-50 p-3 sm:flex-row sm:items-center">
        <div x-cloak x-show="selectedId" class="flex h-32 w-32 max-w-full shrink-0 items-center justify-center rounded border border-slate-200 bg-white">
            <img x-show="selectedUrl &amp;&amp; !previewFailed" x-bind:src="selectedUrl || null" x-on:error="previewFailed = true" alt="Selected media" class="h-full w-full object-contain">
            <span x-show="!selectedUrl || previewFailed" role="status" class="p-3 text-center text-xs text-slate-500">Preview unavailable</span>
        </div>
        <div class="min-w-0 flex-1 space-y-3">
        <p x-cloak x-show="selectedId" x-text="selectedName || 'Selected image'" class="break-words text-sm font-medium text-slate-700" style="overflow-wrap: anywhere;"></p>
        <div class="flex flex-wrap gap-2">
        <button type="button" x-on:click="open()" class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 focus:ring-2 focus:ring-slate-500">Choose / replace image</button>
        @if (! $required)
            <button type="button" x-cloak x-show="selectedId" x-on:click="clear()" class="rounded-md border border-slate-300 px-3 py-2 text-sm text-slate-600 hover:bg-slate-50">Clear image</button>
        @endif
        </div>
        </div>
    </div>
    @error('media_id') <p class="text-sm text-red-600" role="alert">{{ $message }}</p> @enderror
    <noscript><label class="block text-sm text-slate-700">Existing image ID (JPEG/PNG/WebP)<input type="number" min="1" name="media_id" value="{{ $selectedId }}" class="mt-2 block w-full rounded-md border-slate-300"></label></noscript>
    <dialog x-ref="dialog" aria-label="Choose an existing image" class="m-auto max-w-3xl overflow-y-auto rounded-xl bg-white p-5 shadow-xl backdrop:bg-slate-950/60" style="width: calc(100% - 2rem); max-height: calc(100% - 2rem);">
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-lg font-semibold">Choose image</h2>
            <button type="button" autofocus x-on:click="$refs.dialog.close()" class="rounded-md border border-slate-300 px-3 py-2 text-sm">Cancel</button>
        </div>
        <label class="mt-4 block text-sm font-medium">Search images<input type="search" maxlength="255" x-model="search" x-on:keydown.enter.prevent="load(1)" class="mt-2 block w-full rounded-md border-slate-300"></label>
        <button type="button" x-on:click="load(1)" x-bind:disabled="loading" class="mt-3 rounded-md bg-slate-950 px-4 py-2 text-sm text-white">Search</button>
        <p x-show="loading" role="status" class="mt-3 text-sm">Loading images…</p>
        <p x-text="error" role="alert" class="mt-3 text-sm text-red-600"></p>
        <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3">
            <template x-for="item in images" x-bind:key="item.id">
                <button type="button" x-on:click="select(item)" class="min-w-0 rounded-lg border border-slate-200 p-2 text-left hover:border-slate-500 focus:ring-2 focus:ring-slate-500">
                    <img x-bind:src="item.url" alt="" loading="lazy" class="h-28 w-full object-contain">
                    <span x-text="item.name" class="mt-2 block break-all text-xs"></span>
                </button>
            </template>
        </div>
        <p x-show="!loading && !error && images.length === 0" class="mt-4 text-sm text-slate-500">No images found.</p>
        <div class="mt-5 flex flex-wrap items-center justify-between gap-3">
            <button type="button" x-on:click="load(page - 1)" x-bind:disabled="loading || page <= 1" class="rounded-md border px-3 py-2 text-sm disabled:opacity-50">Previous</button>
            <span class="text-sm" x-text="'Page ' + page + ' of ' + lastPage"></span>
            <button type="button" x-on:click="load(page + 1)" x-bind:disabled="loading || page >= lastPage" class="rounded-md border px-3 py-2 text-sm disabled:opacity-50">Next</button>
        </div>
    </dialog>
</div>
