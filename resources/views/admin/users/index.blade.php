<x-admin-layout title="Users"
                description="View CMS users and manage their assigned primary role.">
    <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Name</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Email</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Role</th>
                        @can('users.assign_roles')
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Assign Role</th>
                        @endcan
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @foreach ($users as $user)
                <tr>
                    <td class="whitespace-nowrap px-4 py-4 text-sm font-medium text-slate-950">{{ $user->name }}</td>
                    <td class="whitespace-nowrap px-4 py-4 text-sm text-slate-600">{{ $user->email }}</td>
                    <td class="whitespace-nowrap px-4 py-4 text-sm text-slate-700">{{ $user->roles->pluck('name')->implode(', ') ?: 'None' }}</td>
                    @can('users.assign_roles')
                        <td class="px-4 py-4">
                            <form method="POST" action="{{ route('admin.users.role.update', $user) }}" class="flex flex-wrap items-center gap-2">
                                @csrf
                                @method('PATCH')

                                <label for="role_id_{{ $user->id }}" class="sr-only">Role</label>
                                <select id="role_id_{{ $user->id }}"
                                        name="role_id"
                                        class="rounded-md border-slate-300 text-sm shadow-sm focus:border-slate-500 focus:ring-slate-500">
                                    @foreach ($roles as $role)
                                        @if ($role->name !== \App\Support\Rbac::ROLE_SUPER_ADMIN || auth()->user()?->can('super-admin.manage'))
                                            <option value="{{ $role->id }}" @selected($user->roles->contains('id', $role->id))>
                                                {{ $role->name }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>

                                <button type="submit"
                                        class="rounded-md bg-slate-950 px-3 py-2 text-sm font-medium text-white hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2">
                                    Save
                                </button>
                            </form>
                        </td>
                    @endcan
                </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-admin-layout>
