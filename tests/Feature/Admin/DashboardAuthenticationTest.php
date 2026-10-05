<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_from_admin_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect(route('login', absolute: false));
    }

    public function test_authenticated_user_can_access_admin_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertOk();
    }

    public function test_frontend_homepage_remains_public(): void
    {
        $response = $this->get('/');

        $response->assertOk();
    }
}
