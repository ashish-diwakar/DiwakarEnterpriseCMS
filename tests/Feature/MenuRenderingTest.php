<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Support\MenuResolver;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Tests\Concerns\WithMenuFixtures;
use Tests\TestCase;

class MenuRenderingTest extends TestCase
{
    use WithMenuFixtures;

    public function test_resolves_by_location_and_slug_and_missing_inactive_empty_menus(): void
    {
        $resolver = app(MenuResolver::class);
        $this->assertSame([], $resolver->forLocation('header'));
        $menu = $this->menu(['location' => 'header']);
        $this->assertSame([], $resolver->forSlug($menu->slug));
        $this->item($menu, ['label' => 'Visible']);
        $this->assertSame('Visible', $resolver->forLocation('header')[0]['label']);
        $this->assertSame($resolver->forLocation('header'), $resolver->forSlug($menu->slug));
        $menu->update(['is_active' => false]);
        $this->assertSame([], $resolver->forLocation('header'));
    }

    public function test_hidden_invalid_and_empty_group_ancestors_never_promote_children(): void
    {
        $menu = $this->menu(['location' => 'header']);
        $hidden = $this->item($menu, ['is_active' => false]);
        $invalid = $this->item($menu, ['url' => 'javascript:alert(1)']);
        $this->item($menu, ['parent_id' => $hidden->id, 'label' => 'Hidden descendant']);
        $this->item($menu, ['parent_id' => $invalid->id, 'label' => 'Invalid descendant']);
        $this->item($menu, ['type' => 'group', 'url' => null, 'label' => 'Empty group']);
        $group = $this->item($menu, ['type' => 'group', 'url' => null, 'label' => 'Useful group']);
        $child = $this->item($menu, ['parent_id' => $group->id, 'label' => 'Child']);
        $this->item($menu, ['parent_id' => $child->id, 'label' => 'Grandchild']);
        $nodes = app(MenuResolver::class)->forLocation('header');
        $this->assertCount(1, $nodes);
        $this->assertNull($nodes[0]['url']);
        $this->assertSame('Useful group', $nodes[0]['label']);
        $this->assertSame('Grandchild', $nodes[0]['children'][0]['children'][0]['label']);
    }

    public function test_page_destinations_are_references_and_unresolved_public_routes_are_excluded(): void
    {
        $menu = $this->menu(['location' => 'header']);
        foreach (['draft', 'published'] as $status) {
            $page = Page::create(['title' => $status, 'slug' => $status, 'status' => $status]);
            $item = $this->item($menu, ['type' => 'page', 'url' => null, 'page_id' => $page->id]);
            $page->update(['slug' => 'renamed-'.$status]);
            $this->assertSame($page->id, $item->fresh()->page_id);
        }
        $this->assertSame([], app(MenuResolver::class)->forLocation('header'));
    }

    public function test_publication_and_current_slug_contract_can_be_supplied_by_a_later_renderer(): void
    {
        // Test-only route and resolver: no public Page route is added to the application.
        Route::get('/fixture-content/{slug}', fn () => 'fixture')->name('fixture.public.page');
        Route::getRoutes()->refreshNameLookups();
        $resolver = new class extends MenuResolver
        {
            protected function pageUrl(Page $page): ?string
            {
                return route('fixture.public.page', ['slug' => $page->slug], absolute: false);
            }
        };
        $menu = $this->menu(['location' => 'header']);
        $draft = Page::create(['title' => 'Draft', 'slug' => 'draft']);
        $published = Page::create(['title' => 'Published', 'slug' => 'published', 'status' => 'published']);
        $draftItem = $this->item($menu, ['type' => 'page', 'page_id' => $draft->id, 'url' => null]);
        $this->item($menu, ['parent_id' => $draftItem->id, 'label' => 'Hidden below draft']);
        $this->item($menu, ['type' => 'page', 'page_id' => $published->id, 'url' => null]);
        $published->update(['slug' => 'current-slug']);
        $nodes = $resolver->forLocation('header');
        $this->assertCount(1, $nodes);
        $this->assertSame('/fixture-content/current-slug', $nodes[0]['url']);
    }

    public function test_blade_escapes_labels_attributes_and_adds_safe_new_tab_rel(): void
    {
        $menu = $this->menu(['location' => 'header']);
        $this->item($menu, ['label' => '<img src=x onerror=alert(1)>', 'url' => '/path?a=1&b=2', 'target' => '_blank']);
        $html = Blade::render('<x-navigation-menu location="header" />');
        $this->assertStringContainsString('&lt;img src=x onerror=alert(1)&gt;', $html);
        $this->assertStringNotContainsString('<img src=x', $html);
        $this->assertStringContainsString('/path?a=1&amp;b=2', $html);
        $this->assertStringContainsString('rel="noopener noreferrer"', $html);
        $this->assertStringNotContainsString('menu_id', $html);
        $this->item($menu, ['label' => 'Same tab', 'target' => '_self']);
        $this->get('/')->assertOk()->assertSee('Same tab')->assertSee('Dewatering India Website')->assertSee('Welcome');
    }

    public function test_queries_remain_bounded_as_the_menu_grows(): void
    {
        $menu = $this->menu(['location' => 'header']);
        $page = Page::create(['title' => 'Page', 'slug' => 'page']);
        $this->item($menu, ['type' => 'page', 'url' => null, 'page_id' => $page->id]);
        for ($i = 0; $i < 30; $i++) {
            $this->item($menu);
        }
        DB::enableQueryLog();
        DB::flushQueryLog();
        $nodes = app(MenuResolver::class)->forLocation('header');
        $queries = count(DB::getQueryLog());
        DB::disableQueryLog();
        $this->assertCount(30, $nodes);
        $this->assertLessThanOrEqual(4, $queries);
    }
}
