<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\AssignUserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserRoleRequest;
use App\Models\User;
use App\Support\Rbac;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserRoleController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::query()
                ->with('roles')
                ->orderBy('name')
                ->orderBy('email')
                ->get(),
            'roles' => Role::query()
                ->where('guard_name', Rbac::GUARD_WEB)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function update(UpdateUserRoleRequest $request, User $user, AssignUserRole $assignUserRole): RedirectResponse
    {
        $role = Role::query()
            ->where('guard_name', Rbac::GUARD_WEB)
            ->findOrFail($request->validated('role_id'));

        $assignUserRole->handle($request->user(), $user, $role);

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'User role updated.');
    }
}
