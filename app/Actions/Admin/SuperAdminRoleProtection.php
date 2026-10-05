<?php

namespace App\Actions\Admin;

use App\Models\User;
use App\Support\Rbac;
use Spatie\Permission\Models\Role;

class SuperAdminRoleProtection
{
    public function roleIsSuperAdmin(Role $role): bool
    {
        return $role->guard_name === Rbac::GUARD_WEB
            && $role->name === Rbac::ROLE_SUPER_ADMIN;
    }

    public function userHasSuperAdminRole(User $user): bool
    {
        return $user->roles()
            ->where('name', Rbac::ROLE_SUPER_ADMIN)
            ->where('guard_name', Rbac::GUARD_WEB)
            ->exists();
    }

    public function isLastSuperAdmin(User $user): bool
    {
        if (! $this->userHasSuperAdminRole($user)) {
            return false;
        }

        $superAdminRole = Role::query()
            ->where('name', Rbac::ROLE_SUPER_ADMIN)
            ->where('guard_name', Rbac::GUARD_WEB)
            ->first();

        if (! $superAdminRole) {
            return false;
        }

        return $superAdminRole->users()->count() <= 1;
    }
}
