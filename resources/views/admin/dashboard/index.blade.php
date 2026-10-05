<x-admin-layout title="Dashboard"
                description="Welcome to the CMS administration area.">
    <section class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-base font-semibold text-slate-950">Administration</h2>
        <p class="mt-2 text-sm text-slate-600">
            Use the navigation to manage currently available CMS administration areas.
        </p>

        <div class="mt-5 flex flex-wrap gap-3">
            @can('users.view')
                <a href="{{ route('admin.users.index') }}"
                   class="rounded-md bg-slate-950 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2">
                    Manage Users
                </a>
            @endcan

            @can('roles.view')
                <a href="{{ route('admin.roles.index') }}"
                   class="rounded-md border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2">
                    Manage Roles
                </a>
            @endcan
        </div>
    </section>
</x-admin-layout>
