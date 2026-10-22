@php
    $oldText = fn ($key, $fallback = '') => is_scalar(old($key, $fallback)) ? old($key, $fallback) : '';
@endphp
<section class="flex flex-col gap-6 rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
    <h2 class="text-base font-semibold">Active section type: {{ $section->type->label() }}</h2>
    <p class="text-sm text-slate-500">These fields will be saved as {{ $section->type->label() }}. Plain text only.{{ $creating ? '' : ' A saved section’s type cannot be changed.' }}</p>
    @foreach ($errors->getMessages() as $field => $messages)
        @if (! in_array($field, ['heading', 'body', 'media_id', 'image_alt', 'button_label', 'button_url', 'is_enabled'], true))
            @foreach ($messages as $message) <p class="text-sm text-red-600" role="alert">{{ $message }}</p> @endforeach
        @endif
    @endforeach
    <div>
        <label for="heading" class="mb-2 block text-sm font-medium">Heading</label>
        <input id="heading" name="heading" required maxlength="200" value="{{ $oldText('heading', $section->heading) }}" class="block w-full rounded-md border-slate-300 px-3 py-2.5">
        @error('heading') <p class="mt-2 text-sm text-red-600" role="alert">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="body" class="mb-2 block text-sm font-medium">{{ $section->type === \App\Enums\PageSectionType::Hero ? 'Introduction (optional)' : 'Text' }}</label>
        <textarea id="body" name="body" rows="6" maxlength="{{ match ($section->type) { \App\Enums\PageSectionType::Hero => 500, \App\Enums\PageSectionType::Text => 10000, default => 5000 } }}" @required($section->type !== \App\Enums\PageSectionType::Hero) class="block w-full rounded-md border-slate-300">{{ $oldText('body', $section->body) }}</textarea>
        @error('body') <p class="mt-2 text-sm text-red-600" role="alert">{{ $message }}</p> @enderror
    </div>
    @if ($section->type !== \App\Enums\PageSectionType::Text)
        <x-admin.media-image-picker :page="$page" :image="$image" :required="$section->type === \App\Enums\PageSectionType::ImageText" />
        <div>
            <label for="image_alt" class="mb-2 block text-sm font-medium">Image description (required when an image is selected)</label>
            <input id="image_alt" name="image_alt" maxlength="255" @required($section->type === \App\Enums\PageSectionType::ImageText) value="{{ $oldText('image_alt', $section->image_alt) }}" class="block w-full rounded-md border-slate-300 px-3 py-2.5">
            @error('image_alt') <p class="mt-2 text-sm text-red-600" role="alert">{{ $message }}</p> @enderror
        </div>
    @endif
    @if ($section->type === \App\Enums\PageSectionType::Hero)
        <div>
            <label for="button_label" class="mb-2 block text-sm font-medium">Button label (optional)</label>
            <input id="button_label" name="button_label" maxlength="80" value="{{ $oldText('button_label', $section->button_label) }}" class="block w-full rounded-md border-slate-300 px-3 py-2.5">
            @error('button_label') <p class="mt-2 text-sm text-red-600" role="alert">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="button_url" class="mb-2 block text-sm font-medium">Button destination</label>
            <input id="button_url" name="button_url" maxlength="2048" value="{{ $oldText('button_url', $section->button_url) }}" class="block w-full rounded-md border-slate-300 px-3 py-2.5">
            <p class="mt-2 text-sm text-slate-500">Provide both label and destination, or leave both blank. Use /contact or a complete HTTP/HTTPS URL.</p>
            @error('button_url') <p class="mt-2 text-sm text-red-600" role="alert">{{ $message }}</p> @enderror
        </div>
    @endif
    <div>
        <input type="hidden" name="is_enabled" value="0">
        <label class="flex items-center gap-3 text-sm font-medium"><input type="checkbox" name="is_enabled" value="1" @checked(old('is_enabled', $section->is_enabled)) class="rounded border-slate-300">Enabled</label>
        <p class="mt-2 text-sm text-slate-500">Independent of the Page's Draft/Published status.</p>
        @error('is_enabled') <p class="mt-2 text-sm text-red-600" role="alert">{{ $message }}</p> @enderror
    </div>
    <div class="flex flex-wrap gap-3 border-t border-slate-200 pt-6">
        <button class="rounded-md bg-slate-950 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800 focus:ring-2 focus:ring-slate-500">Save section</button>
        <a href="{{ route('admin.pages.sections.index', $page) }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Cancel</a>
    </div>
</section>
