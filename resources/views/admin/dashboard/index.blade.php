<x-admin-layout title="Dashboard">

    <h1>Dashboard</h1>

    <p>
        Welcome to Diwakar Enterprise CMS.
    </p>

    @can('users.view')
        <p>
            <a href="{{ route('admin.users.index') }}">Manage user roles</a>
        </p>
    @endcan

    @can('roles.view')
        <p>
            <a href="{{ route('admin.roles.index') }}">Manage role permissions</a>
        </p>
    @endcan

</x-admin-layout>
