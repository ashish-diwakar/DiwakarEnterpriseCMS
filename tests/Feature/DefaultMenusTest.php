<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Support\MenuResolver;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\DefaultMenusSeeder;
use Database\Seeders\MenuPermissionsSeeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Tests\Concerns\WithMenuFixtures;
use Tests\TestCase;

class DefaultMenusTest extends TestCase
{
    use WithMenuFixtures;

    public function test_fresh_install_initializes_two_ordered_menus_without_unrelated_sample_data(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->assertDatabaseCount('menus', 2);
        $this->assertDatabaseCount('menu_items', 11);
        $this->assertDatabaseCount('menu_initializations', 1);
        $this->assertDatabaseHas('menus', ['slug' => 'main-navigation', 'location' => 'header', 'is_active' => 1]);
        $this->assertDatabaseHas('menus', ['slug' => 'footer-navigation', 'location' => 'footer', 'is_active' => 1]);
        $this->assertSame(['Home', 'About Us', 'Services', 'Projects', 'Contact Us'], Menu::where('slug', 'main-navigation')->first()->items->pluck('label')->all());
        $this->assertSame([1, 2, 3, 4, 5, 6], Menu::where('slug', 'footer-navigation')->first()->items->pluck('sort_order')->all());
        foreach (['users', 'pages', 'page_sections', 'media'] as $table) {
            $this->assertDatabaseCount($table, 0);
        }
        $this->assertSame(2, MenuItem::where('is_active', true)->count());
        $this->get('/')->assertOk()->assertSee('Main navigation')->assertSee('Footer navigation')->assertDontSee('About Us');
    }

    public function test_repeated_initialization_preserves_all_customizations_and_record_identity(): void
    {
        $this->seed(DefaultMenusSeeder::class);
        $home = MenuItem::where('label', 'Home')->first();
        $home->forceFill(['label' => 'Customized', 'url' => '/changed', 'sort_order' => 5, 'is_active' => false])->save();
        $menu = $home->menu;
        $menu->update(['name' => 'Renamed', 'location' => null, 'is_active' => false]);
        $beforeMenus = DB::table('menus')->orderBy('id')->get()->toArray();
        $beforeItems = DB::table('menu_items')->orderBy('id')->get()->toArray();
        $this->seed(DefaultMenusSeeder::class);
        $this->assertEquals($beforeMenus, DB::table('menus')->orderBy('id')->get()->toArray());
        $this->assertEquals($beforeItems, DB::table('menu_items')->orderBy('id')->get()->toArray());
    }

    public function test_deleted_items_empty_menus_and_deleted_menus_stay_deleted(): void
    {
        $this->seed(DefaultMenusSeeder::class);
        $main = Menu::where('slug', 'main-navigation')->first();
        $footer = Menu::where('slug', 'footer-navigation')->first();
        $main->items()->delete();
        $main->delete();
        $footer->items()->delete();
        $this->seed(DefaultMenusSeeder::class);
        $this->assertDatabaseMissing('menus', ['slug' => 'main-navigation']);
        $this->assertDatabaseCount('menu_items', 0);
        $this->assertSame([], app(MenuResolver::class)->forLocation('footer'));
        $this->get('/')->assertDontSee('Main navigation')->assertDontSee('Footer navigation');
    }

    public function test_existing_identifiers_and_location_assignments_are_not_overwritten(): void
    {
        $existing = $this->menu(['slug' => 'main-navigation', 'name' => 'Custom Main', 'location' => 'header']);
        $customFooter = $this->menu(['slug' => 'custom-footer', 'location' => 'footer']);
        $this->item($customFooter, ['label' => 'Existing custom item']);
        $this->seed(DefaultMenusSeeder::class);
        $this->assertSame('Custom Main', $existing->fresh()->name);
        $this->assertCount(0, $existing->fresh()->items);
        $this->assertDatabaseHas('menus', ['id' => $customFooter->id, 'location' => 'footer']);
        $this->assertDatabaseHas('menus', ['slug' => 'footer-navigation', 'location' => null]);
        $this->assertSame('Existing custom item', app(MenuResolver::class)->forLocation('footer')[0]['label']);
    }

    public function test_normal_admin_edits_are_reflected_without_hardcoded_fallback(): void
    {
        $this->seed(DefaultMenusSeeder::class);
        $this->authorizeMenus();
        $menu = Menu::where('slug', 'main-navigation')->first();
        $home = $menu->items()->where('label', 'Home')->first();
        $this->patch(route('admin.menus.items.update', [$menu, $home]), $this->itemData($menu, ['label' => 'Start here', 'url' => '/?welcome=1']))->assertSessionHasNoErrors();
        $this->seed(DefaultMenusSeeder::class);
        $nodes = app(MenuResolver::class)->forLocation('header');
        $this->assertSame('Start here', $nodes[0]['label']);
        $this->assertSame('/?welcome=1', $nodes[0]['url']);
        $this->assertCount(1, $nodes);
    }

    public function test_permission_only_initialization_does_not_change_any_role_assignments(): void
    {
        $admin = Role::findByName('Admin');
        $admin->syncPermissions(['media.manage']);
        $this->seed(MenuPermissionsSeeder::class);
        $this->seed(MenuPermissionsSeeder::class);
        $this->assertSame(['media.manage'], $admin->fresh()->permissions->pluck('name')->all());
        $this->assertDatabaseCount('menus', 0);
    }

    public function test_initialization_failure_rolls_back_marker_and_partial_defaults(): void
    {
        $failed = false;
        DB::listen(function ($query) use (&$failed): void {
            if (! $failed && str_starts_with($query->sql, 'insert into "menu_items"')) {
                $failed = true;
                throw new \RuntimeException('Synthetic initialization failure');
            }
        });
        try {
            $this->seed(DefaultMenusSeeder::class);
            $this->fail('Expected synthetic initialization failure');
        } catch (\RuntimeException $exception) {
            $this->assertSame('Synthetic initialization failure', $exception->getMessage());
        }
        $this->assertDatabaseCount('menu_initializations', 0);
        $this->assertDatabaseCount('menus', 0);
        $this->assertDatabaseCount('menu_items', 0);
        $this->seed(DefaultMenusSeeder::class);
        $this->assertDatabaseCount('menus', 2);
    }
}
