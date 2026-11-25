<x-admin-layout :title="$menu->name" description="Manage up to three levels. Reordering applies within the current sibling group.">
    <div class="space-y-6" x-data="{ deleteLabel: '', deleteAction: '', deleteVersion: {{ $menu->lock_version }} }">
        <x-admin.menu-errors />
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('admin.menus.index') }}" class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm text-slate-700">All menus</a>
            @can('menus.update')<a href="{{ route('admin.menus.edit', $menu) }}" class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm text-slate-700">Edit menu</a><a href="{{ route('admin.menus.items.create', $menu) }}" class="rounded-md bg-slate-950 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Add item</a>@endcan
            <x-admin.menu-status-badge :active="$menu->is_active" />
            <span class="text-sm text-slate-600">{{ $menu->location ?? 'Unassigned' }}</span>
        </div>
        @if ($menu->description)<p class="break-words text-sm text-slate-600">{{ $menu->description }}</p>@endif
        <div class="space-y-2 rounded-lg border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">
            <p>Configured status and public visibility are separate. Eligibility below reflects the current menu, ancestors and destination; no external availability requests are made.</p>
            @if (! $menu->is_active)<p class="font-medium text-slate-700">This menu is inactive. All of its items are hidden from public navigation.</p>@endif
            @if ($menu->location === null)<p>Unassigned menu location. It is not shown automatically in the header or footer. An active menu with eligible items can still be rendered explicitly by its identifier.</p>@endif
            <p>Published CMS Pages can be assigned to this menu, but their links will not appear on the website until public Page rendering is implemented.</p>
        </div>
        <div class="space-y-3">
            @forelse ($tree as $row)
                @php($item = $row['item'])
                <article id="menu-item-{{ $item->id }}" @class(['min-w-0 rounded-lg border p-4 shadow-sm', 'border-slate-200 bg-white' => $item->is_active, 'border-slate-300 bg-slate-50' => ! $item->is_active]) style="margin-left: {{ ($row['depth'] - 1) * 0.75 }}rem">
                    <div class="flex flex-col justify-between gap-4 sm:flex-row">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-3"><h2 class="min-w-0 break-words font-semibold text-slate-950">{{ $item->label }}</h2><x-admin.menu-status-badge :active="$item->is_active" /></div>
                            <p class="mt-1 text-xs text-slate-600">Level {{ $row['depth'] }} · {{ $item->type->label() }} · Position {{ $item->sort_order }} · {{ $item->target === '_blank' ? 'New tab' : 'Same tab' }}</p>
                            <p class="mt-2 break-all text-sm text-slate-600">@if ($item->type === \App\Enums\MenuItemType::Page){{ $item->page?->title ?? 'Unavailable Page' }} — {{ $item->page?->status?->label() ?? 'Unavailable' }} (public routing deferred)@elseif ($item->type === \App\Enums\MenuItemType::Url){{ $item->url }}@else Non-clickable navigation group @endif</p>
                            <x-admin.menu-item-visibility :assessment="$visibility[$item->id]" :unassigned="$menu->location === null" />
                        </div>
                        @can('menus.update')
                            <div class="flex flex-wrap items-start gap-2">
                                @foreach (['up' => 'Move up', 'down' => 'Move down'] as $direction => $text)
                                    <form method="POST" action="{{ route('admin.menus.items.move', [$menu, $item]) }}">@csrf @method('PATCH')<input type="hidden" name="version" value="{{ $menu->lock_version }}"><input type="hidden" name="direction" value="{{ $direction }}"><button class="rounded-md border border-slate-300 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">{{ $text }}<span class="sr-only"> {{ $item->label }}</span></button></form>
                                @endforeach
                                <a href="{{ route('admin.menus.items.edit', [$menu, $item]) }}" class="rounded-md border border-slate-300 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">Edit</a>
                                <button type="button" data-label="{{ $item->label }}" data-action="{{ route('admin.menus.items.destroy', [$menu, $item]) }}" x-on:click="deleteLabel = $el.dataset.label; deleteAction = $el.dataset.action; $refs.deleteDialog.showModal()" class="rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700 hover:bg-red-100">Delete</button>
                            </div>
                        @endcan
                    </div>
                </article>
            @empty
                <p class="rounded-lg border border-slate-200 bg-white p-6 text-sm text-slate-600">This menu has no items. Empty menus do not render public navigation.</p>
            @endforelse
        </div>
        <x-admin.menu-delete-dialog />
    </div>
</x-admin-layout>
