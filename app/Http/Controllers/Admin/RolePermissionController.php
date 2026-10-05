<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\SuperAdminRoleProtection;
use App\Actions\Admin\SyncRolePermissions;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateRolePermissionsRequest;
use App\Support\Rbac;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    public function index(SuperAdminRoleProtection $superAdminRoleProtection): View
    {
        return view('admin.roles.index', [
            'roles' => Role::query()
                ->with('permissions')
                ->where('guard_name', Rbac::GUARD_WEB)
                ->orderBy('name')
                ->get(),
            'permissions' => Permission::query()
                ->where('guard_name', Rbac::GUARD_WEB)
                ->whereIn('name', Rbac::permissions())
                ->orderBy('name')
                ->get(),
            'superAdminRoleProtection' => $superAdminRoleProtection,
        ]);
    }

    public function update(UpdateRolePermissionsRequest $request, Role $role, SyncRolePermissions $syncRolePermissions): RedirectResponse
    {
        $syncRolePermissions->handle($role, $request->validated('permissions', []));

        return redirect()
            ->route('admin.roles.index')
            ->with('status', 'Role permissions updated.');
    }
}
