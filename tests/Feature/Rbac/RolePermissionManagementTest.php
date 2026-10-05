<?php

namespace Tests\Feature\Rbac;

use App\Models\User;
use App\Support\Rbac;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RolePermissionManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_super_admin_role_permissions_cannot_be_altered_through_normal_management(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(Rbac::ROLE_SUPER_ADMIN);

        $superAdminRole = Role::findByName(Rbac::ROLE_SUPER_ADMIN, Rbac::GUARD_WEB);

        $response = $this
            ->actingAs($superAdmin)
            ->patch(route('admin.roles.permissions.update', $superAdminRole), [
                'permissions' => [Rbac::PERMISSION_ADMIN_ACCESS],
            ]);

        $response->assertForbidden();
        $this->assertCount(0, $superAdminRole->fresh()->permissions);
    }

    public function test_unauthorized_permission_management_request_is_rejected(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Rbac::ROLE_ADMIN);

        $adminRole = Role::findByName(Rbac::ROLE_ADMIN, Rbac::GUARD_WEB);

        $response = $this
            ->actingAs($admin)
            ->patch(route('admin.roles.permissions.update', $adminRole), [
                'permissions' => [Rbac::PERMISSION_USERS_ASSIGN_ROLES],
            ]);

        $response->assertForbidden();
        $this->assertFalse($adminRole->fresh()->hasPermissionTo(Rbac::PERMISSION_USERS_ASSIGN_ROLES));
    }

    public function test_super_admin_can_update_admin_role_permissions_with_known_permissions_only(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(Rbac::ROLE_SUPER_ADMIN);

        $adminRole = Role::findByName(Rbac::ROLE_ADMIN, Rbac::GUARD_WEB);

        $response = $this
            ->actingAs($superAdmin)
            ->patch(route('admin.roles.permissions.update', $adminRole), [
                'permissions' => [
                    Rbac::PERMISSION_ADMIN_ACCESS,
                    Rbac::PERMISSION_USERS_ASSIGN_ROLES,
                ],
            ]);

        $response->assertRedirect(route('admin.roles.index'));
        $this->assertTrue($adminRole->fresh()->hasPermissionTo(Rbac::PERMISSION_USERS_ASSIGN_ROLES));
    }

    public function test_unknown_permission_names_are_rejected(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(Rbac::ROLE_SUPER_ADMIN);

        $adminRole = Role::findByName(Rbac::ROLE_ADMIN, Rbac::GUARD_WEB);

        $response = $this
            ->actingAs($superAdmin)
            ->patch(route('admin.roles.permissions.update', $adminRole), [
                'permissions' => ['pages.delete'],
            ]);

        $response->assertSessionHasErrors('permissions.0');
    }
}
