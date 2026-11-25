<x-admin-layout title="Menus" description="Manage reusable website navigation and locations.">
    <div class="space-y-6" x-data="{ deleteLabel: '', deleteAction: '', deleteVersion: 0 }">
        <x-admin.menu-errors />
        <div class="flex flex-wrap items-end justify-between gap-4">
            <form method="GET" action="{{ route('admin.menus.index') }}" class="flex min-w-0 flex-1 flex-wrap items-end gap-3">
                <div class="min-w-0 flex-1"><label for="search" class="block text-sm font-medium text-slate-700">Search name or identifier</label><input id="search" name="search" type="search" maxlength="200" value="{{ $filters['search'] ?? '' }}" class="mt-1 w-full rounded-md border-slate-300 text-sm"></div>
                <button class="rounded-md bg-slate-950 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Apply</button>
                <a href="{{ route('admin.menus.index') }}" class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Reset</a>
            </form>
            @can('menus.create')<a href="{{ route('admin.menus.create') }}" class="rounded-md bg-slate-950 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Create menu</a>@endcan
        </div>
        <div class="grid gap-4 md:grid-cols-2">
            @forelse ($menus as $menu)
                <article id="menu-{{ $menu->id }}" @class(['min-w-0 rounded-lg border p-5 shadow-sm', 'border-slate-200 bg-white' => $menu->is_active, 'border-slate-300 bg-slate-50' => ! $menu->is_active])>
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <h2 class="min-w-0 break-words text-lg font-semibold text-slate-950"><a href="{{ route('admin.menus.show', $menu) }}" class="hover:underline">{{ $menu->name }}</a></h2>
                        <x-admin.menu-status-badge :active="$menu->is_active" />
                    </div>
                    <p class="mt-1 break-all text-sm text-slate-600">{{ $menu->slug }}</p>
                    <dl class="mt-3 space-y-1 text-sm text-slate-600">
                        <div><dt class="inline font-medium">Location: </dt><dd class="inline">{{ \App\Enums\MenuLocation::tryFrom($menu->location ?? '')?->label() ?? 'Unassigned' }}</dd></div>
                        <div><dt class="inline font-medium">Items: </dt><dd class="inline">{{ $menu->items_count }}</dd></div>
                        <div><dt class="inline font-medium">Updated: </dt><dd class="inline">{{ $menu->updated_at->format('d M Y, H:i') }}</dd></div>
                    </dl>
                    @if (! $menu->is_active)<p class="mt-3 text-sm text-slate-600">Public navigation is hidden because this menu is inactive.</p>@endif
                    @if ($menu->location === null)<p class="mt-3 text-sm text-slate-600">Unassigned location. When active with eligible items, this menu can be rendered explicitly by its identifier.</p>@endif
                    @if ($menu->is_active)<p class="mt-3 text-xs text-slate-500">Active status does not guarantee public links. View items for visibility details.</p>@endif
                    <div class="mt-4 flex flex-wrap gap-3">
                        <a href="{{ route('admin.menus.show', $menu) }}" class="rounded-md border border-slate-300 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">View items</a>
                        @can('menus.update')<a href="{{ route('admin.menus.edit', $menu) }}" class="rounded-md border border-slate-300 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">Edit menu</a>@endcan
                        @can('menus.delete')<button type="button" data-label="{{ $menu->name }}" data-action="{{ route('admin.menus.destroy', $menu) }}" x-on:click="deleteLabel = $el.dataset.label; deleteAction = $el.dataset.action; $refs.deleteDialog.showModal()" class="rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700 hover:bg-red-100">Delete</button>@endcan
                    </div>
                </article>
            @empty
                <p class="rounded-lg border border-slate-200 bg-white p-6 text-sm text-slate-600 md:col-span-2">No menus found.</p>
            @endforelse
        </div>
        {{ $menus->links() }}
        <x-admin.menu-delete-dialog />
    </div>
</x-admin-layout>
