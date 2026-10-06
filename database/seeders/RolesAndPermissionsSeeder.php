<?php

namespace Database\Seeders;

use App\Support\Rbac;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (Rbac::permissions() as $permission) {
            Permission::findOrCreate($permission, Rbac::GUARD_WEB);
        }

        Role::findOrCreate(Rbac::ROLE_SUPER_ADMIN, Rbac::GUARD_WEB);

        Role::findOrCreate(Rbac::ROLE_ADMIN, Rbac::GUARD_WEB)
            ->givePermissionTo(Rbac::adminPermissions());

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
