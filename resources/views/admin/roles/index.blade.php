<x-admin-layout title="Roles">

    <h1>Roles</h1>

    @if (session('status'))
        <p>{{ session('status') }}</p>
    @endif

    @foreach ($roles as $role)
        <section>
            <h2>{{ $role->name }}</h2>

            @if ($superAdminRoleProtection->roleIsSuperAdmin($role))
                <p>Protected full-access role. Permissions are granted through the centralized Super Admin authorization bypass.</p>
            @else
                <form method="POST" action="{{ route('admin.roles.permissions.update', $role) }}">
                    @csrf
                    @method('PATCH')

                    @foreach ($permissions as $permission)
                        <label>
                            <input type="checkbox"
                                   name="permissions[]"
                                   value="{{ $permission->name }}"
                                   @checked($role->permissions->contains('name', $permission->name))
                                   @cannot('roles.manage_permissions') disabled @endcannot>
                            {{ $permission->name }}
                        </label>
                    @endforeach

                    @can('roles.manage_permissions')
                        <button type="submit">Save Permissions</button>
                    @endcan
                </form>
            @endif
        </section>
    @endforeach

</x-admin-layout>
