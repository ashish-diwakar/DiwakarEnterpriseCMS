<x-admin-layout title="Users">

    <h1>Users</h1>

    @if (session('status'))
        <p>{{ session('status') }}</p>
    @endif

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                @can('users.assign_roles')
                    <th>Assign Role</th>
                @endcan
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->roles->pluck('name')->implode(', ') ?: 'None' }}</td>
                    @can('users.assign_roles')
                        <td>
                            <form method="POST" action="{{ route('admin.users.role.update', $user) }}">
                                @csrf
                                @method('PATCH')

                                <label for="role_id_{{ $user->id }}">Role</label>
                                <select id="role_id_{{ $user->id }}" name="role_id">
                                    @foreach ($roles as $role)
                                        @if ($role->name !== \App\Support\Rbac::ROLE_SUPER_ADMIN || auth()->user()?->can('super-admin.manage'))
                                            <option value="{{ $role->id }}" @selected($user->roles->contains('id', $role->id))>
                                                {{ $role->name }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>

                                <button type="submit">Save</button>
                            </form>
                        </td>
                    @endcan
                </tr>
            @endforeach
        </tbody>
    </table>

</x-admin-layout>
