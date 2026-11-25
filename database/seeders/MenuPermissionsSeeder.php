<?php

namespace Database\Seeders;

use App\Support\Rbac;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class MenuPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([Rbac::PERMISSION_MENUS_VIEW, Rbac::PERMISSION_MENUS_CREATE, Rbac::PERMISSION_MENUS_UPDATE, Rbac::PERMISSION_MENUS_DELETE] as $permission) {
            Permission::findOrCreate($permission, Rbac::GUARD_WEB);
        }
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
