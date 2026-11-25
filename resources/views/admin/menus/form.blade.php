<x-admin-layout :title="$menu->exists ? 'Edit menu' : 'Create menu'" description="Choose a stable identifier and optionally assign one navigation location.">
    <form method="POST" action="{{ $menu->exists ? route('admin.menus.update', $menu) : route('admin.menus.store') }}" class="space-y-6">
        @csrf @if ($menu->exists) @method('PATCH') @endif
        <x-admin.menu-errors />
        <section class="space-y-5 rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <div><label for="name" class="mb-2 block text-sm font-medium">Name</label><input id="name" name="name" required maxlength="200" value="{{ old('name', $menu->name) }}" class="w-full rounded-md border-slate-300"></div>
            <div><label for="slug" class="mb-2 block text-sm font-medium">Identifier</label><input id="slug" name="slug" maxlength="150" @required($menu->exists) value="{{ old('slug', $menu->slug) }}" pattern="[a-z0-9]+(-[a-z0-9]+)*" class="w-full rounded-md border-slate-300"><p class="mt-2 text-xs text-slate-500">Lowercase letters, numbers and hyphens. Leave blank on creation to derive it from the name; later renaming does not automatically change it.</p></div>
            <div><label for="description" class="mb-2 block text-sm font-medium">Description (optional)</label><textarea id="description" name="description" maxlength="2000" rows="3" class="w-full rounded-md border-slate-300">{{ old('description', $menu->description) }}</textarea></div>
            <div><label for="location" class="mb-2 block text-sm font-medium">Location</label><select id="location" name="location" class="w-full rounded-md border-slate-300"><option value="">Unassigned / reusable</option>@foreach ($locations as $location)<option value="{{ $location->value }}" @selected(old('location', $menu->location) === $location->value)>{{ $location->label() }}</option>@endforeach</select><p class="mt-2 text-xs text-slate-500">Each location can have one menu. Remove its existing assignment before reassigning it.</p></div>
            <div><input type="hidden" name="is_active" value="0"><label class="flex items-center gap-3"><input name="is_active" type="checkbox" value="1" @checked(old('is_active', $menu->is_active)) class="rounded border-slate-300 text-slate-950"> <span class="text-sm">Active</span></label></div>
            <div class="flex flex-wrap gap-3 border-t border-slate-200 pt-5"><button class="rounded-md bg-slate-950 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Save menu</button><a href="{{ route('admin.menus.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Cancel</a></div>
        </section>
    </form>
</x-admin-layout>
