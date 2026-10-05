<?php

namespace App\Actions\Admin;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class AssignUserRole
{
    public function __construct(
        private readonly SuperAdminRoleProtection $superAdminRoleProtection
    ) {}

    /**
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function handle(User $actor, User $target, Role $role): void
    {
        $selectedRoleIsSuperAdmin = $this->superAdminRoleProtection->roleIsSuperAdmin($role);
        $targetHasSuperAdminRole = $this->superAdminRoleProtection->userHasSuperAdminRole($target);

        if (($selectedRoleIsSuperAdmin || $targetHasSuperAdminRole)
            && Gate::forUser($actor)->denies('super-admin.manage')) {
            throw new AuthorizationException('Only a Super Admin may assign or remove the Super Admin role.');
        }

        if ($targetHasSuperAdminRole
            && ! $selectedRoleIsSuperAdmin
            && $this->superAdminRoleProtection->isLastSuperAdmin($target)) {
            throw ValidationException::withMessages([
                'role_id' => 'The last remaining Super Admin cannot be demoted.',
            ]);
        }

        $target->syncRoles([$role]);
    }
}
