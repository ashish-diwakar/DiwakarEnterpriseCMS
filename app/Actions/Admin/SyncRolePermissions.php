<?php

namespace App\Actions\Admin;

use Illuminate\Auth\Access\AuthorizationException;
use Spatie\Permission\Models\Role;

class SyncRolePermissions
{
    public function __construct(
        private readonly SuperAdminRoleProtection $superAdminRoleProtection
    ) {}

    /**
     * @param  list<string>  $permissionNames
     *
     * @throws AuthorizationException
     */
    public function handle(Role $role, array $permissionNames): void
    {
        if ($this->superAdminRoleProtection->roleIsSuperAdmin($role)) {
            throw new AuthorizationException('The Super Admin role permissions are protected.');
        }

        $role->syncPermissions($permissionNames);
    }
}
