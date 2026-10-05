<?php

namespace Tests\Feature\Rbac;

use App\Models\User;
use App\Support\Rbac;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleAssignmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_super_admin_can_assign_admin_role(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(Rbac::ROLE_SUPER_ADMIN);

        $target = User::factory()->create();
        $adminRole = Role::findByName(Rbac::ROLE_ADMIN, Rbac::GUARD_WEB);

        $response = $this
            ->actingAs($superAdmin)
            ->patch(route('admin.users.role.update', $target), [
                'role_id' => $adminRole->id,
            ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertTrue($target->fresh()->hasRole(Rbac::ROLE_ADMIN));
        $this->assertCount(1, $target->fresh()->roles);
    }

    public function test_unauthorized_role_assignment_is_rejected(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Rbac::ROLE_ADMIN);

        $target = User::factory()->create();
        $adminRole = Role::findByName(Rbac::ROLE_ADMIN, Rbac::GUARD_WEB);

        $response = $this
            ->actingAs($admin)
            ->patch(route('admin.users.role.update', $target), [
                'role_id' => $adminRole->id,
            ]);

        $response->assertForbidden();
        $this->assertFalse($target->fresh()->hasRole(Rbac::ROLE_ADMIN));
    }

    public function test_non_super_admin_with_assignment_permission_cannot_assign_super_admin(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Rbac::ROLE_ADMIN);
        Role::findByName(Rbac::ROLE_ADMIN, Rbac::GUARD_WEB)
            ->givePermissionTo(Rbac::PERMISSION_USERS_ASSIGN_ROLES);

        $target = User::factory()->create();
        $superAdminRole = Role::findByName(Rbac::ROLE_SUPER_ADMIN, Rbac::GUARD_WEB);

        $response = $this
            ->actingAs($admin)
            ->patch(route('admin.users.role.update', $target), [
                'role_id' => $superAdminRole->id,
            ]);

        $response->assertForbidden();
        $this->assertFalse($target->fresh()->hasRole(Rbac::ROLE_SUPER_ADMIN));
    }

    public function test_non_super_admin_with_assignment_permission_cannot_remove_super_admin(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Rbac::ROLE_ADMIN);
        Role::findByName(Rbac::ROLE_ADMIN, Rbac::GUARD_WEB)
            ->givePermissionTo(Rbac::PERMISSION_USERS_ASSIGN_ROLES);

        $target = User::factory()->create();
        $target->assignRole(Rbac::ROLE_SUPER_ADMIN);

        $adminRole = Role::findByName(Rbac::ROLE_ADMIN, Rbac::GUARD_WEB);

        $response = $this
            ->actingAs($admin)
            ->patch(route('admin.users.role.update', $target), [
                'role_id' => $adminRole->id,
            ]);

        $response->assertForbidden();
        $this->assertTrue($target->fresh()->hasRole(Rbac::ROLE_SUPER_ADMIN));
    }

    public function test_last_super_admin_cannot_be_demoted(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(Rbac::ROLE_SUPER_ADMIN);

        $adminRole = Role::findByName(Rbac::ROLE_ADMIN, Rbac::GUARD_WEB);

        $response = $this
            ->actingAs($superAdmin)
            ->patch(route('admin.users.role.update', $superAdmin), [
                'role_id' => $adminRole->id,
            ]);

        $response->assertSessionHasErrors('role_id');
        $this->assertTrue($superAdmin->fresh()->hasRole(Rbac::ROLE_SUPER_ADMIN));
    }
}
