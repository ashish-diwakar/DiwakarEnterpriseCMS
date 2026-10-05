<header class="sticky top-0 z-20 border-b border-slate-200 bg-white/95 backdrop-blur">
    <div class="flex h-16 items-center gap-4 px-4 sm:px-6 lg:px-8">
        <button type="button"
                class="inline-flex h-10 w-10 items-center justify-center rounded-md border border-slate-200 text-slate-700 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-500 lg:hidden"
                aria-controls="admin-sidebar"
                :aria-expanded="sidebarOpen.toString()"
                x-on:click="sidebarOpen = true">
            <span class="sr-only">Open admin navigation</span>
            <svg class="h-5 w-5"
                 viewBox="0 0 20 20"
                 fill="currentColor"
                 aria-hidden="true">
                <path fill-rule="evenodd"
                      d="M2 4.75A.75.75 0 0 1 2.75 4h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 4.75ZM2 10a.75.75 0 0 1 .75-.75h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 10Zm.75 4.5a.75.75 0 0 0 0 1.5h14.5a.75.75 0 0 0 0-1.5H2.75Z"
                      clip-rule="evenodd" />
            </svg>
        </button>

        <div class="min-w-0 flex-1">
            <p class="truncate text-sm font-semibold text-slate-950">Diwakar Enterprise CMS</p>
        </div>

        <div class="relative">
            <button type="button"
                    class="flex items-center gap-3 rounded-md px-2 py-1.5 text-left hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-500"
                    aria-haspopup="true"
                    :aria-expanded="accountOpen.toString()"
                    x-on:click="accountOpen = ! accountOpen"
                    x-on:keydown.escape.window="accountOpen = false">
                <span class="hidden min-w-0 text-right sm:block">
                    <span class="block truncate text-sm font-medium text-slate-950">{{ $user?->name }}</span>
                    <span class="block truncate text-xs text-slate-500">{{ $primaryRole }}</span>
                </span>
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-slate-900 text-sm font-semibold text-white">
                    {{ strtoupper(substr($user?->name ?? 'U', 0, 1)) }}
                </span>
            </button>

            <div x-cloak
                 x-show="accountOpen"
                 x-transition
                 x-on:click.outside="accountOpen = false"
                 class="absolute right-0 z-30 mt-2 w-64 rounded-md border border-slate-200 bg-white p-2 shadow-lg">
                <div class="border-b border-slate-100 px-3 py-2">
                    <p class="truncate text-sm font-medium text-slate-950">{{ $user?->name }}</p>
                    <p class="truncate text-xs text-slate-500">{{ $primaryRole }}</p>
                </div>

                <form method="POST" action="{{ route('logout', absolute: false) }}" class="mt-2">
                    @csrf

                    <button type="submit"
                            class="w-full rounded-md px-3 py-2 text-left text-sm text-slate-700 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-500">
                        Log out
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
