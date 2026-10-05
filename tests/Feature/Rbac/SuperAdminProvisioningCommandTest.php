<?php

namespace Tests\Feature\Rbac;

use App\Models\User;
use App\Support\Rbac;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminProvisioningCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_existing_user_can_be_promoted_to_super_admin_without_password(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create([
            'email' => 'admin@example.com',
        ]);

        $this->artisan('cms:provision-super-admin', ['email' => $user->email])
            ->expectsOutput('Super Admin provisioning completed.')
            ->assertSuccessful();

        $this->assertTrue($user->fresh()->hasRole(Rbac::ROLE_SUPER_ADMIN));
    }
}
