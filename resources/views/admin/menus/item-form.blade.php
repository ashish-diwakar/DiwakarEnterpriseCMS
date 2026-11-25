<x-admin-layout :title="$item->exists ? 'Edit menu item' : 'Add menu item'" :description="$menu->name">
    <form method="POST" action="{{ $item->exists ? route('admin.menus.items.update', [$menu, $item]) : route('admin.menus.items.store', $menu) }}" class="space-y-6" x-data="{ type: @js(old('type', $item->type?->value ?? 'url')) }">
        @csrf @if ($item->exists) @method('PATCH') @endif
        <input type="hidden" name="version" value="{{ old('version', $menu->lock_version) }}">
        <x-admin.menu-errors />
        @if ($item->exists)
            <section class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                <p class="mb-2 text-sm font-medium text-slate-700">Currently saved configuration</p>
                <x-admin.menu-status-badge :active="$item->is_active" />
                <x-admin.menu-item-visibility :assessment="$visibility" :unassigned="$menu->location === null" />
                <p class="mt-2 text-xs text-slate-500">Unsaved form changes are not reflected in this assessment.</p>
            </section>
        @endif
        <section class="space-y-5 rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <div><label for="label" class="mb-2 block text-sm font-medium">Display label</label><input id="label" name="label" required maxlength="200" value="{{ old('label', $item->label) }}" class="w-full rounded-md border-slate-300"></div>
            <div><label for="type" class="mb-2 block text-sm font-medium">Item type</label><select id="type" name="type" x-model="type" class="w-full rounded-md border-slate-300">@foreach ($types as $type)<option value="{{ $type->value }}" @selected(old('type', $item->type?->value) === $type->value)>{{ $type->label() }}</option>@endforeach</select></div>
            <div x-show="type === 'page'"><label for="page_id" class="mb-2 block text-sm font-medium">CMS Page</label><select id="page_id" name="page_id" x-bind:required="type === 'page'" x-bind:disabled="type !== 'page'" class="w-full rounded-md border-slate-300"><option value="">Choose a Page</option>@foreach ($pages as $page)<option value="{{ $page->id }}" @selected((string) old('page_id', $item->page_id) === (string) $page->id)>{{ $page->title }} — {{ $page->status->label() }}</option>@endforeach</select><p class="mt-2 text-sm text-slate-600">Published CMS Pages can be assigned to this menu, but their links will not appear on the website until public Page rendering is implemented.</p><p class="mt-2 text-xs text-slate-500">Draft Pages can also be selected in advance and remain unpublished.</p></div>
            <div x-show="type === 'url'"><label for="url" class="mb-2 block text-sm font-medium">Custom URL</label><input id="url" name="url" maxlength="2048" value="{{ old('url', $item->url) }}" x-bind:required="type === 'url'" x-bind:disabled="type !== 'url'" class="w-full rounded-md border-slate-300"><p class="mt-2 text-xs text-slate-500">Use /path, /path?query=value#fragment, or an HTTP/HTTPS URL without credentials.</p></div>
            <p x-show="type === 'group'" class="text-sm text-slate-600">Groups have no destination and appear publicly only when they contain eligible children.</p>
            <div><label for="parent_id" class="mb-2 block text-sm font-medium">Parent item</label><select id="parent_id" name="parent_id" class="w-full rounded-md border-slate-300"><option value="">Root level</option>@foreach ($parents as $row)<option value="{{ $row['item']->id }}" @disabled($row['item']->id === $item->id) @selected((string) old('parent_id', $item->parent_id) === (string) $row['item']->id)>{{ str_repeat('— ', $row['depth'] - 1) }}{{ $row['item']->label }}</option>@endforeach</select><p class="mt-2 text-xs text-slate-500">Maximum three levels including the entire moved subtree. Moving to a new parent appends the item to that sibling group.</p></div>
            <div x-show="type !== 'group'"><label for="target" class="mb-2 block text-sm font-medium">Open link</label><select id="target" name="target" class="w-full rounded-md border-slate-300"><option value="_self" @selected(old('target', $item->target) === '_self')>Same tab</option><option value="_blank" @selected(old('target', $item->target) === '_blank')>New tab</option></select></div>
            <div><input type="hidden" name="is_active" value="0"><label class="flex items-center gap-3"><input name="is_active" type="checkbox" value="1" @checked(old('is_active', $item->is_active)) class="rounded border-slate-300 text-slate-950"><span class="text-sm">Active</span></label></div>
            <div class="flex flex-wrap gap-3 border-t border-slate-200 pt-5"><button class="rounded-md bg-slate-950 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Save item</button><a href="{{ route('admin.menus.show', $menu) }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Cancel</a></div>
        </section>
    </form>
</x-admin-layout>
