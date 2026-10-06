<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\WebsiteSetting;
use App\Support\Rbac;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class WebsiteSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_guest_cannot_access_settings_administration(): void
    {
        $response = $this->get(route('admin.settings.edit'));

        $response->assertRedirect(route('login', absolute: false));
    }

    public function test_authenticated_user_without_settings_permission_is_forbidden(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Rbac::ROLE_ADMIN);

        $this->actingAs($user)
            ->get(route('admin.settings.edit'))
            ->assertForbidden();

        $this->actingAs($user)
            ->patch(route('admin.settings.update'), $this->validPayload())
            ->assertForbidden();
    }

    public function test_super_admin_can_manage_settings_through_gate_bypass(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(Rbac::ROLE_SUPER_ADMIN);

        $this->actingAs($superAdmin)
            ->get(route('admin.settings.edit'))
            ->assertOk()
            ->assertSee('Website Settings');

        $this->actingAs($superAdmin)
            ->patch(route('admin.settings.update'), $this->validPayload([
                'site_name' => 'Super Admin Site',
            ]))
            ->assertRedirect(route('admin.settings.edit'));

        $this->assertDatabaseHas('website_settings', [
            'id' => WebsiteSetting::SINGLETON_ID,
            'site_name' => 'Super Admin Site',
        ]);
    }

    public function test_authorized_user_can_view_and_update_settings(): void
    {
        $admin = $this->adminWithSettingsPermission();

        $this->actingAs($admin)
            ->get(route('admin.settings.edit'))
            ->assertOk()
            ->assertSee('Site name')
            ->assertSee('Public email')
            ->assertSee('Facebook URL')
            ->assertDontSee('Logo')
            ->assertDontSee('Favicon');

        $this->actingAs($admin)
            ->patch(route('admin.settings.update'), $this->validPayload([
                'site_name' => 'Example CMS',
                'public_email' => 'hello@example.test',
                'facebook_url' => 'https://www.facebook.com/example',
            ]))
            ->assertRedirect(route('admin.settings.edit'))
            ->assertSessionHas('status', 'Website settings updated.');

        $this->assertDatabaseHas('website_settings', [
            'id' => WebsiteSetting::SINGLETON_ID,
            'site_name' => 'Example CMS',
            'public_email' => 'hello@example.test',
            'facebook_url' => 'https://www.facebook.com/example',
        ]);
    }

    public function test_settings_page_handles_missing_record_without_creating_it(): void
    {
        $admin = $this->adminWithSettingsPermission();

        $this->assertDatabaseCount('website_settings', 0);

        $this->actingAs($admin)
            ->get(route('admin.settings.edit'))
            ->assertOk()
            ->assertSee('Website Settings');

        $this->assertDatabaseCount('website_settings', 0);
    }

    public function test_first_save_creates_singleton_and_subsequent_save_updates_same_record(): void
    {
        $admin = $this->adminWithSettingsPermission();

        $this->actingAs($admin)
            ->patch(route('admin.settings.update'), $this->validPayload([
                'site_name' => 'Initial Site',
            ]))
            ->assertRedirect(route('admin.settings.edit'));

        $this->assertDatabaseCount('website_settings', 1);
        $this->assertDatabaseHas('website_settings', [
            'id' => WebsiteSetting::SINGLETON_ID,
            'site_name' => 'Initial Site',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.settings.update'), $this->validPayload([
                'site_name' => 'Updated Site',
            ]))
            ->assertRedirect(route('admin.settings.edit'));

        $this->assertDatabaseCount('website_settings', 1);
        $this->assertDatabaseHas('website_settings', [
            'id' => WebsiteSetting::SINGLETON_ID,
            'site_name' => 'Updated Site',
        ]);
    }

    public function test_settings_navigation_visibility_follows_settings_permission(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Rbac::ROLE_ADMIN);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertDontSee('>Settings</span>', false);

        $authorized = $this->adminWithSettingsPermission();

        $this->actingAs($authorized)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('>Settings</span>', false);
    }

    public function test_invalid_email_is_rejected(): void
    {
        $admin = $this->adminWithSettingsPermission();

        $this->actingAs($admin)
            ->patch(route('admin.settings.update'), $this->validPayload([
                'public_email' => 'not-an-email',
            ]))
            ->assertSessionHasErrors('public_email');
    }

    public function test_invalid_social_urls_are_rejected(): void
    {
        $admin = $this->adminWithSettingsPermission();

        foreach ([
            'facebook_url',
            'instagram_url',
            'linkedin_url',
            'youtube_url',
            'x_twitter_url',
        ] as $field) {
            $this->actingAs($admin)
                ->patch(route('admin.settings.update'), $this->validPayload([
                    $field => 'javascript:alert(1)',
                ]))
                ->assertSessionHasErrors($field);
        }
    }

    public function test_script_like_footer_text_is_escaped_when_redisplayed(): void
    {
        $admin = $this->adminWithSettingsPermission();
        $footerText = "<script>alert('test')</script>";

        $this->actingAs($admin)
            ->patch(route('admin.settings.update'), $this->validPayload([
                'footer_text' => $footerText,
            ]))
            ->assertRedirect(route('admin.settings.edit'));

        $this->actingAs($admin)
            ->get(route('admin.settings.edit'))
            ->assertOk()
            ->assertSee(e($footerText), false)
            ->assertDontSee($footerText, false);
    }

    public function test_overlong_values_are_rejected(): void
    {
        $admin = $this->adminWithSettingsPermission();

        $this->actingAs($admin)
            ->patch(route('admin.settings.update'), $this->validPayload([
                'site_name' => str_repeat('A', 121),
                'footer_text' => str_repeat('B', 501),
            ]))
            ->assertSessionHasErrors(['site_name', 'footer_text']);
    }

    public function test_unapproved_request_fields_cannot_be_persisted(): void
    {
        $admin = $this->adminWithSettingsPermission();

        $this->actingAs($admin)
            ->patch(route('admin.settings.update'), $this->validPayload([
                'site_name' => 'Safe Public Site',
                'app_key' => 'base64:secret',
                'database_password' => 'secret',
                'logo_path' => '/unexpected-logo.png',
            ]))
            ->assertRedirect(route('admin.settings.edit'));

        $settings = WebsiteSetting::query()->firstOrFail();

        $this->assertSame('Safe Public Site', $settings->site_name);
        $this->assertArrayNotHasKey('app_key', $settings->getAttributes());
        $this->assertArrayNotHasKey('database_password', $settings->getAttributes());
        $this->assertArrayNotHasKey('logo_path', $settings->getAttributes());
    }

    /**
     * @param  array<string, string>  $overrides
     * @return array<string, string>
     */
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'site_name' => 'Example Website',
            'tagline' => 'A reusable CMS website',
            'public_email' => 'contact@example.test',
            'primary_phone' => '+1 555 100 2000',
            'secondary_phone' => '+1 555 100 3000',
            'address' => '123 Example Street',
            'facebook_url' => 'https://www.facebook.com/example',
            'instagram_url' => 'https://www.instagram.com/example',
            'linkedin_url' => 'https://www.linkedin.com/company/example',
            'youtube_url' => 'https://www.youtube.com/@example',
            'x_twitter_url' => 'https://x.com/example',
            'footer_text' => 'Example footer text.',
        ], $overrides);
    }

    private function adminWithSettingsPermission(): User
    {
        $adminRole = Role::findByName(Rbac::ROLE_ADMIN, Rbac::GUARD_WEB);
        $adminRole->givePermissionTo(Rbac::PERMISSION_SETTINGS_MANAGE);

        $admin = User::factory()->create();
        $admin->assignRole(Rbac::ROLE_ADMIN);

        return $admin;
    }
}
