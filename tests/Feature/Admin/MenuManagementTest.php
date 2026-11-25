<?php

namespace Tests\Feature\Admin;

use App\Models\Menu;
use App\Models\User;
use App\Support\Rbac;
use Database\Seeders\RolesAndPermissionsSeeder;
use Spatie\Permission\Models\Role;
use Tests\Concerns\WithMenuFixtures;
use Tests\TestCase;

class MenuManagementTest extends TestCase
{
    use WithMenuFixtures;

    public function test_guest_and_unauthorized_direct_routes_are_rejected(): void
    {
        $menu = $this->menu();
        $item = $this->item($menu);
        $routes = [
            ['get', 'admin.menus.index', []], ['get', 'admin.menus.create', []], ['post', 'admin.menus.store', []],
            ['get', 'admin.menus.show', [$menu]], ['get', 'admin.menus.edit', [$menu]], ['patch', 'admin.menus.update', [$menu]], ['delete', 'admin.menus.destroy', [$menu]],
            ['get', 'admin.menus.items.create', [$menu]], ['post', 'admin.menus.items.store', [$menu]], ['patch', 'admin.menus.items.reorder', [$menu]],
            ['get', 'admin.menus.items.edit', [$menu, $item]], ['patch', 'admin.menus.items.update', [$menu, $item]], ['delete', 'admin.menus.items.destroy', [$menu, $item]], ['patch', 'admin.menus.items.move', [$menu, $item]],
        ];
        foreach ($routes as [$method, $name, $parameters]) {
            $this->$method(route($name, $parameters))->assertRedirect(route('login'));
        }
        foreach ([[], ['admin.access'], ['menus.view', 'menus.update', 'menus.create', 'menus.delete']] as $permissions) {
            $user = User::factory()->create();
            $user->givePermissionTo($permissions);
            $this->actingAs($user);
            foreach ($routes as [$method, $name, $parameters]) {
                $this->$method(route($name, $parameters))->assertForbidden();
            }
        }
    }

    public function test_specific_permissions_and_super_admin_bypass(): void
    {
        $menu = $this->menu();
        $this->authorizeMenus(['menus.view']);
        $this->get(route('admin.menus.show', $menu))->assertOk()->assertDontSee('Add item');
        $this->get(route('admin.menus.create'))->assertForbidden();
        $this->patch(route('admin.menus.update', $menu))->assertForbidden();
        $this->delete(route('admin.menus.destroy', $menu))->assertForbidden();
        $this->authorizeMenus(['menus.view', 'menus.create']);
        $this->get(route('admin.menus.create'))->assertOk();
        $this->post(route('admin.menus.items.store', $menu), $this->itemData($menu))->assertForbidden();
        $this->authorizeMenus(['menus.view', 'menus.update']);
        $this->post(route('admin.menus.items.store', $menu), $this->itemData($menu))->assertSessionHasNoErrors();
        $this->delete(route('admin.menus.destroy', $menu))->assertForbidden();
        $super = User::factory()->create()->assignRole(Rbac::ROLE_SUPER_ADMIN);
        $this->actingAs($super)->get(route('admin.menus.create'))->assertOk();
        $this->post(route('admin.menus.store'), ['name' => 'Super menu', 'is_active' => 1])->assertSessionHasNoErrors();
    }

    public function test_permission_seeding_preserves_admin_customizations_and_navigation_visibility(): void
    {
        $role = Role::findByName(Rbac::ROLE_ADMIN);
        $role->givePermissionTo('media.manage');
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->assertTrue($role->fresh()->hasPermissionTo('media.manage'));
        foreach (['menus.view', 'menus.create', 'menus.update', 'menus.delete'] as $permission) {
            $this->assertFalse($role->fresh()->hasPermissionTo($permission));
        }
        $user = User::factory()->create()->assignRole($role);
        $this->actingAs($user)->get(route('admin.dashboard'))->assertDontSee(route('admin.menus.index'), false);
        $user->givePermissionTo('menus.view');
        $this->get(route('admin.dashboard'))->assertSee(route('admin.menus.index'), false);
    }

    public function test_create_edit_search_status_and_location_removal(): void
    {
        $this->authorizeMenus();
        $this->post(route('admin.menus.store'), ['name' => 'Main Navigation', 'location' => 'header', 'is_active' => 1, 'lock_version' => 999])->assertSessionHasNoErrors();
        $menu = Menu::firstOrFail();
        $this->assertSame('main-navigation', $menu->slug);
        $this->assertSame(0, $menu->lock_version);
        $this->get(route('admin.menus.edit', $menu))->assertOk()->assertSee('name="_token"', false);
        $this->patch(route('admin.menus.update', $menu), ['name' => 'Renamed', 'slug' => $menu->slug, 'description' => 'Reusable', 'location' => '', 'is_active' => 0])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('menus', ['id' => $menu->id, 'name' => 'Renamed', 'location' => null, 'is_active' => false]);
        $this->get(route('admin.menus.index', ['search' => 'Renamed']))->assertOk()->assertSee('Renamed');
        $this->get(route('admin.menus.index', ['search' => 'main-navigation']))->assertSee('Renamed');
        $this->get(route('admin.menus.index', ['search' => 'missing']))->assertSee('No menus found.')->assertDontSee('Renamed');
    }

    public function test_duplicate_identifiers_locations_invalid_fields_and_multiple_unassigned_menus(): void
    {
        $this->authorizeMenus();
        $this->menu(['slug' => 'main', 'location' => 'header']);
        $this->post(route('admin.menus.store'), ['name' => 'Duplicate', 'slug' => 'main', 'location' => 'header', 'is_active' => 1])->assertSessionHasErrors(['slug', 'location']);
        $this->post(route('admin.menus.store'), ['name' => '', 'slug' => '../unsafe', 'location' => 'unknown', 'is_active' => 'bad'])->assertSessionHasErrors(['name', 'slug', 'location', 'is_active']);
        foreach (['secondary', 'tertiary'] as $slug) {
            $this->post(route('admin.menus.store'), ['name' => $slug, 'slug' => $slug, 'is_active' => 1])->assertSessionHasNoErrors();
        }
        $this->assertDatabaseCount('menus', 3);
        $this->assertDatabaseHas('menus', ['slug' => 'main', 'location' => 'header']);
    }

    public function test_menu_deletion_requires_explicit_item_removal_and_missing_records_404(): void
    {
        $this->authorizeMenus();
        $menu = $this->menu();
        $item = $this->item($menu, ['is_active' => false]);
        $this->delete(route('admin.menus.destroy', $menu))->assertSessionHasErrors('menu');
        $item->delete();
        $this->delete(route('admin.menus.destroy', $menu))->assertRedirect(route('admin.menus.index'));
        $this->assertDatabaseMissing('menus', ['id' => $menu->id]);
        $this->get(route('admin.menus.show', $menu))->assertNotFound();
    }

    public function test_listing_is_paginated_and_user_labels_are_escaped(): void
    {
        $this->authorizeMenus();
        $menu = $this->menu(['name' => '<script>menu()</script>']);
        $this->get(route('admin.menus.index'))->assertSee('&lt;script&gt;menu()&lt;/script&gt;', false)->assertDontSee('<script>menu()</script>', false);
        for ($i = 0; $i < 22; $i++) {
            $this->menu();
        }
        $this->get(route('admin.menus.index'))->assertViewHas('menus', fn ($menus) => $menus->count() === 20 && $menus->total() === 23);
        $this->get(route('admin.menus.index', ['page' => 2]))->assertSee(e($menu->name), false);
    }
}
