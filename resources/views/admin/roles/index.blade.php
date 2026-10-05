<x-admin-layout title="Roles"
                description="Review CMS roles and manage approved permissions.">
    <div class="grid gap-4">
        @foreach ($roles as $role)
        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-lg font-semibold text-slate-950">{{ $role->name }}</h2>
            </div>

            @if ($superAdminRoleProtection->roleIsSuperAdmin($role))
                <p class="mt-3 text-sm text-slate-600">
                    Protected full-access role. Permissions are granted through the centralized Super Admin authorization bypass.
                </p>
            @else
                <form method="POST" action="{{ route('admin.roles.permissions.update', $role) }}" class="mt-4">
                    @csrf
                    @method('PATCH')

                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($permissions as $permission)
                            <label class="flex items-center gap-3 rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-700">
                                <input type="checkbox"
                                       name="permissions[]"
                                       value="{{ $permission->name }}"
                                       class="rounded border-slate-300 text-slate-950 focus:ring-slate-500"
                                       @checked($role->permissions->contains('name', $permission->name))
                                       @cannot('roles.manage_permissions') disabled @endcannot>
                                <span>{{ $permission->name }}</span>
                            </label>
                        @endforeach
                    </div>

                    @can('roles.manage_permissions')
                        <button type="submit"
                                class="mt-4 rounded-md bg-slate-950 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2">
                            Save Permissions
                        </button>
                    @endcan
                </form>
            @endif
        </section>
        @endforeach
    </div>
</x-admin-layout>
