<x-admin-layout title="Media Library" description="Upload and manage public website assets.">
    <div class="space-y-6">
        <section class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-base font-semibold text-slate-950">Upload media</h2>
            <p id="file-help" class="mt-2 text-sm text-slate-600">Choose up to 10 files. JPEG, PNG or WebP up to 5 MiB each. PDF up to 10 MiB each. Uploaded files are public. Server request limits may restrict the total upload size.</p>
            <form method="POST" action="{{ route('admin.media.store') }}" enctype="multipart/form-data" class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-start">
                @csrf
                <div class="min-w-0 flex-1">
                    <label for="files" class="sr-only">Choose media files</label>
                    <input id="files" name="files[]" type="file" multiple required accept=".jpg,.jpeg,.png,.webp,.pdf" aria-describedby="file-help" class="block w-full min-w-0 rounded-md border border-slate-300 bg-white p-2 text-sm text-slate-700 file:mr-3 file:rounded-md file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-slate-800 hover:file:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2">
                    @foreach (array_merge($errors->get('files'), $errors->get('files.*')) as $messages)
                        @foreach ((array) $messages as $message)
                            <p class="mt-2 text-sm text-red-600" role="alert">{{ $message }}</p>
                        @endforeach
                    @endforeach
                </div>
                <button class="inline-flex min-h-10 items-center justify-center rounded-md border border-transparent bg-slate-950 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2">Upload</button>
            </form>
        </section>

        <form method="GET" action="{{ route('admin.media.index') }}" class="grid items-end gap-4 rounded-lg border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-[minmax(0,1fr)_auto_auto]">
            <div class="min-w-0 flex-1">
                <label for="search" class="block text-sm font-medium text-slate-700">Search display names or filenames</label>
                <input id="search" name="search" type="search" maxlength="255" value="{{ $filters['search'] ?? '' }}" class="mt-1 min-h-10 w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-slate-500 focus:ring-slate-500">
                @error('search') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="type" class="block text-sm font-medium text-slate-700">Type</label>
                <select id="type" name="type" class="mt-1 min-h-10 w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-slate-500 focus:ring-slate-500">
                    @foreach (['all' => 'All', 'images' => 'Images', 'documents' => 'Documents'] as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['type'] ?? 'all') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('type') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <button class="inline-flex min-h-10 items-center justify-center rounded-md border border-transparent bg-slate-950 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2">Apply</button>
                <a href="{{ route('admin.media.index') }}" class="inline-flex min-h-10 items-center justify-center rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2">Reset</a>
            </div>
        </form>

        <div class="grid min-w-0 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @forelse ($mediaItems as $media)
                <article class="min-w-0 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm" style="align-self: start;"
                         x-data="mediaDisplayName" data-original-name="{{ $media->original_name }}" data-title="{{ $media->title }}" data-draft="{{ old('media_id') == $media->id ? old('title') : $media->title }}">
                    <div class="relative w-full overflow-hidden border-b border-slate-200 bg-slate-100" style="height: 12rem;">
                    @if (in_array($media->mime_type, ['image/jpeg', 'image/png', 'image/webp'], true))
                        <div class="h-full w-full" x-data="{ previewFailed: false }">
                            <img src="{{ $media->url() }}" alt="" loading="lazy"
                                 class="block h-full w-full object-contain"
                                 style="width: 100%; height: 100%; object-fit: contain;"
                                 x-show="!previewFailed" x-on:error="previewFailed = true">
                            <div x-cloak x-show="previewFailed" role="status" class="absolute inset-0 flex items-center justify-center p-4 text-center text-sm text-slate-500">Image preview unavailable. Try Open asset.</div>
                        </div>
                    @else
                        <div class="flex h-full w-full flex-col items-center justify-center gap-2 text-slate-600" style="height: 100%;">
                            <svg class="h-10 w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6Z M14 2v6h6 M8 13h8 M8 17h6" />
                            </svg>
                            <span class="text-sm font-semibold">PDF document</span>
                        </div>
                    @endif
                    </div>
                    <div class="space-y-2 p-4">
                        <div class="space-y-1" style="min-height: 7rem;">
                        <h2 class="break-all text-sm font-semibold text-slate-950" x-text="displayName">{{ $media->title ?? $media->original_name }}</h2>
                        <p x-cloak x-show="title !== null" class="break-all text-xs text-slate-500">{{ $media->original_name }}</p>
                        <p class="text-xs text-slate-600">{{ $media->mime_type }} · {{ $media->size >= 1048576 ? number_format($media->size / 1048576, 1).' MiB' : ($media->size >= 1024 ? number_format($media->size / 1024, 1).' KiB' : $media->size.' B') }}</p>
                        @if ($media->width !== null)
                            <p class="text-xs text-slate-600">{{ $media->width }} × {{ $media->height }} px</p>
                        @endif
                        <p class="text-xs text-slate-600">Uploaded {{ $media->created_at->format('d M Y, H:i') }}</p>
                        </div>
                        <details class="rounded-md border border-slate-200 text-sm" @if (old('media_id') == $media->id) open @endif>
                            <summary class="cursor-pointer px-3 py-2 font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-500">Edit display name</summary>
                            <form method="POST" action="{{ route('admin.media.update', $media) }}" x-on:submit.prevent="save($event.currentTarget)" x-bind:aria-busy="saving" class="space-y-3 border-t border-slate-200 p-3">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="media_id" value="{{ $media->id }}">
                                <label for="title-{{ $media->id }}" class="block text-xs font-medium text-slate-700">Display name</label>
                                <input id="title-{{ $media->id }}" name="title" type="text" maxlength="255" x-model="draft" x-bind:aria-invalid="error !== ''" aria-describedby="title-error-{{ $media->id }}" value="{{ old('media_id') == $media->id ? old('title') : $media->title }}" class="block w-full min-w-0 rounded-md border-slate-300 text-sm focus:border-slate-500 focus:ring-slate-500">
                                <p class="text-xs text-slate-500">Leave blank to use the original filename. The asset URL stays the same.</p>
                                @if (old('media_id') == $media->id)
                                    @error('title') <p class="text-xs text-red-600" role="alert">{{ $message }}</p> @enderror
                                @endif
                                <p id="title-error-{{ $media->id }}" x-cloak x-show="error" x-text="error" class="text-xs text-red-600" role="alert"></p>
                                <div class="flex flex-wrap items-center gap-3 border-t border-slate-100" style="margin-top: 1rem; padding: 1rem 0.75rem 0.75rem;">
                                    <button type="submit" x-bind:disabled="saving" x-text="saving ? 'Saving…' : 'Save display name'" class="inline-flex items-center justify-center rounded-md border border-transparent bg-slate-950 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 disabled:opacity-50">Save display name</button>
                                    <button type="button" x-bind:disabled="saving" x-on:click="cancel($event.currentTarget)" class="inline-flex items-center justify-center rounded-md border border-slate-300 bg-slate-100 px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 disabled:opacity-50">Cancel</button>
                                </div>
                            </form>
                        </details>
                        <p x-text="message" role="status" class="text-xs text-emerald-700" style="min-height: 1rem;"></p>
                        <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
                            <a href="{{ $media->url() }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2">Open asset<span class="sr-only" x-text="': ' + displayName">: {{ $media->title ?? $media->original_name }}</span></a>
                            <form method="POST" action="{{ route('admin.media.destroy', $media) }}" onsubmit="return confirm('Permanently delete this public asset? This cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button class="inline-flex items-center justify-center rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm font-medium text-red-700 shadow-sm hover:border-red-300 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">Delete<span class="sr-only" x-text="' ' + displayName"> {{ $media->title ?? $media->original_name }}</span></button>
                            </form>
                        </div>
                    </div>
                </article>
            @empty
                <p class="col-span-full rounded-lg border border-slate-200 bg-white p-6 text-sm text-slate-600">No media found.</p>
            @endforelse
        </div>
        {{ $mediaItems->links() }}
    </div>
</x-admin-layout>
