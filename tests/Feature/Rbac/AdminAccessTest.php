<?php

namespace Tests\Feature\Rbac;

use App\Models\User;
use App\Support\Rbac;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_guest_admin_access_redirects_to_login(): void
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect(route('login', absolute: false));
    }

    public function test_authenticated_user_without_admin_permission_receives_forbidden(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/dashboard');

        $response->assertForbidden();
    }

    public function test_admin_can_access_dashboard(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Rbac::ROLE_ADMIN);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertOk();
    }

    public function test_super_admin_can_access_dashboard_through_gate_bypass(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(Rbac::ROLE_SUPER_ADMIN);

        $response = $this->actingAs($superAdmin)->get('/admin/dashboard');

        $response->assertOk();
    }

    public function test_admin_receives_only_approved_default_permissions(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Rbac::ROLE_ADMIN);

        $this->assertTrue($admin->can(Rbac::PERMISSION_ADMIN_ACCESS));
        $this->assertTrue($admin->can(Rbac::PERMISSION_USERS_VIEW));
        $this->assertTrue($admin->can(Rbac::PERMISSION_ROLES_VIEW));
        $this->assertFalse($admin->can(Rbac::PERMISSION_USERS_ASSIGN_ROLES));
        $this->assertFalse($admin->can(Rbac::PERMISSION_ROLES_MANAGE_PERMISSIONS));
    }
}
