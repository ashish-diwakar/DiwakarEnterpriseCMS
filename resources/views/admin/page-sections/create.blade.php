<x-admin-layout title="Add Section" description="Add structured content to the selected Page.">
    <p class="mb-5 break-words text-sm text-slate-600">Page: {{ $page->title }}</p>
    <div x-data="{ dirty: @js(session()->hasOldInput()), pendingType: @js($section->type->value), applyingType: false }" x-on:media-selection-changed="dirty = true">
    <form x-ref="typeForm" method="GET" action="{{ route('admin.pages.sections.create', $page) }}" x-on:submit="if (pendingType === @js($section->type->value)) { $event.preventDefault() } else if (dirty) { $event.preventDefault(); $refs.typeDialog.showModal() }" class="mb-6 flex flex-wrap items-end gap-3 rounded-lg border border-slate-200 bg-white p-5">
        <div><label for="choose-type" class="mb-2 block text-sm font-medium">Choose section type</label>
            <select id="choose-type" name="type" x-model="pendingType" aria-describedby="type-help" class="rounded-md border-slate-300">
                @foreach (\App\Enums\PageSectionType::cases() as $type)
                    <option value="{{ $type->value }}" @selected($section->type === $type)>{{ $type->label() }}</option>
                @endforeach
            </select>
        </div>
        <button x-ref="applyTypeButton" class="rounded-md bg-slate-950 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Use this type</button>
        <p id="type-help" class="w-full text-sm text-slate-600">Choose a type, then select Use this type to load its fields. Changing type starts a new form and discards unsaved content.</p>
        <p x-cloak x-show="pendingType !== @js($section->type->value)" role="status" class="w-full rounded-md bg-amber-50 p-3 text-sm text-amber-900">Selection not applied. The active form below will still save as {{ $section->type->label() }}.</p>
    </form>
    <form method="POST" action="{{ route('admin.pages.sections.store', $page) }}" x-on:input="if ($event.target.name) dirty = true" x-on:change="if ($event.target.name) dirty = true">
        @csrf
        <input type="hidden" name="type" value="{{ $section->type->value }}">
        @include('admin.page-sections.partials.form')
    </form>
    <dialog x-ref="typeDialog" aria-labelledby="change-type-heading" aria-describedby="change-type-message" x-on:close="$refs.applyTypeButton.focus()" class="m-auto max-w-lg overflow-y-auto rounded-xl bg-white p-6 shadow-xl backdrop:bg-slate-950/60" style="width: calc(100% - 2rem); max-height: calc(100% - 2rem);">
        <h2 id="change-type-heading" class="text-lg font-semibold text-slate-900">Change section type?</h2>
        <p id="change-type-message" class="mt-3 text-sm text-slate-600">Changing the section type will discard your unsaved content. Do you want to continue?</p>
        <div class="mt-6 flex flex-wrap justify-end gap-3 border-t border-slate-200 pt-5">
            <button type="button" autofocus x-on:click="$refs.typeDialog.close()" class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 focus:ring-2 focus:ring-slate-500">Cancel</button>
            <button type="button" x-bind:disabled="applyingType" x-on:click="if (!applyingType) { applyingType = true; $refs.typeForm.submit() }" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 focus:ring-2 focus:ring-red-500 disabled:opacity-50">Discard changes</button>
        </div>
    </dialog>
    </div>
</x-admin-layout>
