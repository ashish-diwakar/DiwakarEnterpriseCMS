<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class DefaultMenusSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            DB::table('menu_initializations')->insertOrIgnore(['key' => 'default-menus-v1', 'completed_at' => null]);
            $marker = DB::table('menu_initializations')->where('key', 'default-menus-v1')->lockForUpdate()->first();
            if ($marker->completed_at !== null) {
                return;
            }
            $definitions = [
                ['main-navigation', 'Main Navigation', 'header', ['Home', 'About Us', 'Services', 'Projects', 'Contact Us']],
                ['footer-navigation', 'Footer Navigation', 'footer', ['Home', 'About Us', 'Services', 'Contact Us', 'Privacy Policy', 'Terms & Conditions']],
            ];
            foreach ($definitions as [$slug, $name, $location, $labels]) {
                // Existing records (even an intentionally empty menu) are never merged/reset.
                if (Menu::query()->where('slug', $slug)->exists()) {
                    continue;
                }
                $menu = Menu::create(['name' => $name, 'slug' => $slug, 'is_active' => true, 'location' => Menu::query()->where('location', $location)->exists() ? null : $location]);
                foreach ($labels as $index => $label) {
                    $home = $label === 'Home' && Route::has('home');
                    $item = new MenuItem(['label' => $label, 'type' => $home ? 'url' : 'group', 'url' => $home ? route('home', absolute: false) : null, 'target' => '_self', 'is_active' => $home]);
                    $item->forceFill(['menu_id' => $menu->id, 'sort_order' => $index + 1])->saveOrFail();
                }
            }
            DB::table('menu_initializations')->where('key', 'default-menus-v1')->update(['completed_at' => now()]);
        });
    }
}
