<?php

namespace Tests\Concerns;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\User;
use App\Support\Rbac;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

trait WithMenuFixtures
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function authorizeMenus(array $permissions = ['menus.view', 'menus.create', 'menus.update', 'menus.delete']): User
    {
        $user = User::factory()->create();
        $user->givePermissionTo([Rbac::PERMISSION_ADMIN_ACCESS, ...$permissions]);
        $this->actingAs($user);

        return $user;
    }

    private function menu(array $attributes = []): Menu
    {
        return Menu::create(array_replace(['name' => 'Navigation', 'slug' => 'menu-'.Menu::count(), 'is_active' => true], $attributes));
    }

    private function item(Menu $menu, array $attributes = []): MenuItem
    {
        return MenuItem::forceCreate(array_replace([
            'menu_id' => $menu->id, 'parent_id' => null, 'label' => 'Home', 'type' => 'url', 'url' => '/',
            'target' => '_self', 'is_active' => true, 'sort_order' => $menu->items()->count() + 1,
        ], $attributes));
    }

    private function itemData(Menu $menu, array $attributes = []): array
    {
        return array_replace(['version' => $menu->fresh()->lock_version, 'label' => 'Item', 'type' => 'url', 'url' => '/contact?via=menu#form', 'target' => '_self', 'parent_id' => null, 'is_active' => 1], $attributes);
    }
}
