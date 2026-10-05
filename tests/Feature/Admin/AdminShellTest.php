<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Support\Rbac;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminShellTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_authorized_admin_dashboard_renders_shell_navigation_and_logout(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Rbac::ROLE_ADMIN);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('Diwakar Enterprise CMS');
        $response->assertSee('Admin navigation');
        $response->assertSee('Dashboard');
        $response->assertSee('Users');
        $response->assertSee('Roles');
        $response->assertSee('Log out');
        $response->assertSee('method="POST"', false);
        $response->assertSee('action="'.route('logout', absolute: false).'"', false);
    }

    public function test_users_navigation_visibility_follows_users_view_permission(): void
    {
        $adminRole = Role::findByName(Rbac::ROLE_ADMIN, Rbac::GUARD_WEB);
        $adminRole->syncPermissions([
            Rbac::PERMISSION_ADMIN_ACCESS,
            Rbac::PERMISSION_ROLES_VIEW,
        ]);

        $admin = User::factory()->create();
        $admin->assignRole(Rbac::ROLE_ADMIN);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertDontSee('>Users</span>', false);
        $response->assertSee('>Roles</span>', false);
    }

    public function test_roles_navigation_visibility_follows_roles_view_permission(): void
    {
        $adminRole = Role::findByName(Rbac::ROLE_ADMIN, Rbac::GUARD_WEB);
        $adminRole->syncPermissions([
            Rbac::PERMISSION_ADMIN_ACCESS,
            Rbac::PERMISSION_USERS_VIEW,
        ]);

        $admin = User::factory()->create();
        $admin->assignRole(Rbac::ROLE_ADMIN);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('>Users</span>', false);
        $response->assertDontSee('>Roles</span>', false);
    }

    public function test_users_page_renders_through_common_admin_shell(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Rbac::ROLE_ADMIN);

        $response = $this->actingAs($admin)->get(route('admin.users.index'));

        $response->assertOk();
        $response->assertSee('Diwakar Enterprise CMS');
        $response->assertSee('View CMS users and manage their assigned primary role.');
    }

    public function test_roles_page_renders_through_common_admin_shell(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Rbac::ROLE_ADMIN);

        $response = $this->actingAs($admin)->get(route('admin.roles.index'));

        $response->assertOk();
        $response->assertSee('Diwakar Enterprise CMS');
        $response->assertSee('Review CMS roles and manage approved permissions.');
    }
}
