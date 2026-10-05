<aside id="admin-sidebar"
       class="flex h-full flex-col border-r border-slate-200 bg-slate-950 text-white">
    <div class="flex h-16 items-center justify-between border-b border-white/10 px-5">
        <a href="{{ route('admin.dashboard') }}"
           class="text-sm font-semibold tracking-wide text-white">
            Diwakar Enterprise CMS
        </a>

        @if ($mobile ?? false)
            <button type="button"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-md text-slate-300 hover:bg-white/10 hover:text-white focus:outline-none focus:ring-2 focus:ring-white"
                    x-on:click="sidebarOpen = false">
                <span class="sr-only">Close admin navigation</span>
                <svg class="h-5 w-5"
                     viewBox="0 0 20 20"
                     fill="currentColor"
                     aria-hidden="true">
                    <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
                </svg>
            </button>
        @endif
    </div>

    <nav class="flex-1 space-y-6 overflow-y-auto px-3 py-5" aria-label="Admin navigation">
        <div>
            <p class="px-3 text-xs font-semibold uppercase tracking-wider text-slate-400">General</p>
            <div class="mt-2 space-y-1">
                <x-admin.sidebar-link :href="route('admin.dashboard')"
                                      :active="request()->routeIs('admin.dashboard')"
                                      x-on:click="sidebarOpen = false">
                    Dashboard
                </x-admin.sidebar-link>
            </div>
        </div>

        <div>
            <p class="px-3 text-xs font-semibold uppercase tracking-wider text-slate-400">Administration</p>
            <div class="mt-2 space-y-1">
                @can('users.view')
                    <x-admin.sidebar-link :href="route('admin.users.index')"
                                          :active="request()->routeIs('admin.users.*')"
                                          x-on:click="sidebarOpen = false">
                        Users
                    </x-admin.sidebar-link>
                @endcan

                @can('roles.view')
                    <x-admin.sidebar-link :href="route('admin.roles.index')"
                                          :active="request()->routeIs('admin.roles.*')"
                                          x-on:click="sidebarOpen = false">
                        Roles
                    </x-admin.sidebar-link>
                @endcan
            </div>
        </div>
    </nav>
</aside>
