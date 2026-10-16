<section class="flex flex-col gap-6 rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
    <p class="text-sm text-slate-600">This manages page identity only. Page content and public rendering will be available in later features.</p>
    <div>
        <label for="title" class="mb-2 block text-sm font-medium text-slate-700">Title</label>
        <input id="title" name="title" type="text" required maxlength="200" value="{{ old('title', $page->title) }}"
               @if ($creating) x-model="title" x-on:input="suggest()" @endif
               class="block w-full rounded-md border-slate-300 px-3 py-2.5 shadow-sm focus:border-slate-500 focus:ring-slate-500" style="min-height: 2.75rem;">
        @error('title') <p class="mt-2 text-sm text-red-600" role="alert">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="slug" class="mb-2 block text-sm font-medium text-slate-700">Slug</label>
        <input id="slug" name="slug" type="text" maxlength="150" value="{{ old('slug', $page->slug) }}" aria-describedby="slug-help"
               @if ($creating) x-model="slug" x-on:input="manuallyEdited = true" @else required @endif
               class="block w-full rounded-md border-slate-300 px-3 py-2.5 shadow-sm focus:border-slate-500 focus:ring-slate-500" style="min-height: 2.75rem;">
        @error('slug') <p class="mt-2 text-sm text-red-600" role="alert">{{ $message }}</p> @enderror
        <p id="slug-help" class="mt-2 text-sm text-slate-500">
            @if ($creating)
                Suggested from the title until you edit it. Leave blank to derive it when saving. The server normalizes the final slug.
            @else
                Changing the title does not change this slug. Changing the slug changes the eventual public URL; redirects are not created.
            @endif
            Use a single segment, such as about-us, without slashes.
        </p>
    </div>
    <div>
        <label for="status" class="mb-2 block text-sm font-medium text-slate-700">Status</label>
        <select id="status" name="status" required class="block w-full rounded-md border-slate-300 px-3 py-2.5 shadow-sm focus:border-slate-500 focus:ring-slate-500" style="min-height: 2.75rem;">
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(old('status', $page->status->value) === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>
        @error('status') <p class="mt-2 text-sm text-red-600" role="alert">{{ $message }}</p> @enderror
        <p class="mt-2 text-sm text-slate-500">Published marks the page as eligible for future public rendering; it does not expose a public page yet.</p>
    </div>
    <div class="flex flex-wrap items-center gap-3 border-t border-slate-200 pt-6" style="margin-top: 0.5rem;">
        <button type="submit" class="inline-flex items-center justify-center rounded-md bg-slate-950 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2">{{ $creating ? 'Create page' : 'Save changes' }}</button>
        <a href="{{ route('admin.pages.index') }}" class="inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2">Cancel</a>
    </div>
</section>
