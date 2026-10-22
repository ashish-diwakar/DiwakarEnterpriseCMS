<?php

namespace Tests\Feature\Admin;

use App\Models\Media;
use App\Models\Page;
use App\Models\User;
use App\Support\Rbac;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageSectionImagePickerTest extends TestCase
{
    use RefreshDatabase;

    public function test_picker_form_uses_a_same_origin_image_endpoint(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $page = Page::create(['title' => 'Page', 'slug' => 'page']);
        $user = User::factory()->create();
        $user->givePermissionTo([Rbac::PERMISSION_ADMIN_ACCESS, Rbac::PERMISSION_PAGES_MANAGE]);

        config(['app.url' => 'http://localhost']);
        config(['filesystems.disks.public.url' => 'http://localhost/storage']);
        $image = Media::create(['original_name' => 'Tiger_pic.jpg', 'disk' => 'public', 'path' => 'media/tiger.jpg', 'mime_type' => 'image/jpeg', 'size' => 10]);
        foreach (['http://127.0.0.1:8000', 'https://staging.example.test', 'https://www.example.test'] as $origin) {
            $this->actingAs($user)
                ->get($origin.'/admin/pages/'.$page->id.'/sections/create')
                ->assertOk()
                ->assertSee('data-endpoint="/admin/pages/'.$page->id.'/sections/images"', false)
                ->assertDontSee('data-endpoint="http://localhost/', false);
            $this->getJson($origin.'/admin/pages/'.$page->id.'/sections/images?search=Tiger_pic&page=1')
                ->assertOk()
                ->assertJsonPath('data.0.id', $image->id)
                ->assertJsonPath('data.0.url', $image->url())
                ->assertJsonPath('data.0.url', '/storage/media/tiger.jpg')
                ->assertJsonPath('current_page', 1);
        }
    }

    public function test_picker_permissions_image_only_search_and_pagination(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $page = Page::create(['title' => 'Page', 'slug' => 'page']);
        $url = route('admin.pages.sections.images', $page);
        $this->get($url)->assertRedirect(route('login'));
        $user = User::factory()->create();
        $user->givePermissionTo(Rbac::PERMISSION_ADMIN_ACCESS);
        $this->actingAs($user)->getJson($url)->assertForbidden();
        $user->givePermissionTo(Rbac::PERMISSION_PAGES_MANAGE);
        $this->assertFalse($user->can(Rbac::PERMISSION_MEDIA_MANAGE));
        for ($i = 0; $i < 25; $i++) {
            Media::create(['original_name' => 'image-'.$i.'.png', 'title' => $i === 0 ? 'Special title' : null, 'disk' => 'public', 'path' => 'media/'.$i.'.png', 'mime_type' => ['image/jpeg', 'image/png', 'image/webp'][$i % 3], 'size' => 10]);
        }
        Media::create(['original_name' => 'image.pdf', 'disk' => 'public', 'path' => 'media/a.pdf', 'mime_type' => 'application/pdf', 'size' => 10]);
        $this->getJson($url)->assertOk()->assertJsonCount(24, 'data')->assertJsonPath('last_page', 2)->assertJsonPath('data.0.name', 'image-24.png')->assertJsonMissingPath('data.0.disk')->assertJsonMissingPath('data.0.path');
        $this->getJson($url.'?page=2')->assertJsonCount(1, 'data');
        $this->getJson($url.'?search=Special')->assertJsonCount(1, 'data')->assertJsonPath('data.0.name', 'Special title');
        $this->getJson($url.'?search=image-24')->assertJsonCount(1, 'data');
        $this->getJson($url.'?search=.pdf')->assertJsonCount(0, 'data');
        $this->getJson($url.'?page=0')->assertUnprocessable();
    }
}
