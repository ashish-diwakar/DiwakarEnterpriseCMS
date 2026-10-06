<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Support\Rbac;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_guest_is_redirected_to_login_from_admin_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect(route('login', absolute: false));
    }

    public function test_authenticated_user_without_admin_access_is_forbidden_from_admin_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertForbidden();
    }

    public function test_authorized_admin_can_render_dashboard(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Rbac::ROLE_ADMIN);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('CMS overview');
        $response->assertDontSee('Quick administration');
    }

    public function test_super_admin_can_render_dashboard_through_gate_bypass(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(Rbac::ROLE_SUPER_ADMIN);

        $response = $this->actingAs($superAdmin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('Users');
        $response->assertSee('Roles');
        $response->assertSee('Permissions');
    }

    public function test_users_summary_and_action_follow_users_view_permission(): void
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
        $response->assertSee('Users');
        $response->assertSee('View Users');
        $response->assertSee('href="'.route('admin.users.index').'"', false);
        $response->assertDontSee('Roles');
        $response->assertDontSee('Permissions');
        $response->assertDontSee('View Roles');
    }

    public function test_users_summary_and_action_are_absent_without_users_view_permission(): void
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
        $response->assertDontSee('Users');
        $response->assertDontSee('View Users');
        $response->assertDontSee('href="'.route('admin.users.index').'"', false);
        $response->assertSee('Roles');
        $response->assertSee('Permissions');
    }

    public function test_roles_and_permissions_summaries_follow_roles_view_permission(): void
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
        $response->assertSee('Roles');
        $response->assertSee('Permissions');
        $response->assertSee('View Roles');
        $response->assertSee('href="'.route('admin.roles.index').'"', false);
        $response->assertDontSee('Users');
        $response->assertDontSee('View Users');
    }

    public function test_roles_action_and_permissions_summary_are_absent_without_roles_view_permission(): void
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
        $response->assertDontSee('Roles');
        $response->assertDontSee('Permissions');
        $response->assertDontSee('View Roles');
        $response->assertDontSee('href="'.route('admin.roles.index').'"', false);
        $response->assertSee('Users');
    }

    public function test_permissions_count_reflects_configured_web_guard_permissions(): void
    {
        Permission::findOrCreate('content.view', Rbac::GUARD_WEB);
        Permission::findOrCreate('api.permission', 'api');

        $admin = User::factory()->create();
        $admin->assignRole(Rbac::ROLE_ADMIN);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSeeInOrder([
            'Permissions',
            '6',
            'Configured CMS permissions for the web guard.',
        ]);
    }

    public function test_dashboard_renders_without_summary_permissions(): void
    {
        $adminRole = Role::findByName(Rbac::ROLE_ADMIN, Rbac::GUARD_WEB);
        $adminRole->syncPermissions([
            Rbac::PERMISSION_ADMIN_ACCESS,
        ]);

        $admin = User::factory()->create();
        $admin->assignRole(Rbac::ROLE_ADMIN);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('CMS overview');
        $response->assertDontSee('Quick administration');
        $response->assertDontSee('Users');
        $response->assertDontSee('Roles');
        $response->assertDontSee('Permissions');
        $response->assertDontSee('View Users');
        $response->assertDontSee('View Roles');
    }

    public function test_dashboard_does_not_show_future_module_placeholders(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Rbac::ROLE_ADMIN);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertDontSee('Website Settings');
        $response->assertDontSee('Media');
        $response->assertDontSee('Pages');
        $response->assertDontSee('SEO');
        $response->assertDontSee('Services');
        $response->assertDontSee('Projects');
        $response->assertDontSee('Blog');
    }
}
