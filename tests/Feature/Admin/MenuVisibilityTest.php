<?php

namespace Tests\Feature\Admin;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use App\Support\MenuResolver;
use DOMDocument;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\TestResponse;
use Tests\Concerns\WithMenuFixtures;
use Tests\TestCase;

class MenuVisibilityTest extends TestCase
{
    use WithMenuFixtures;

    public function test_menu_badges_and_muted_cards_reflect_saved_status_without_disabling_actions(): void
    {
        $this->authorizeMenus();
        $active = $this->menu(['name' => 'Active menu']);
        $inactive = $this->menu(['name' => 'Inactive menu', 'is_active' => false]);
        $response = $this->get(route('admin.menus.index'))->assertOk();
        $activeHtml = $this->card($response, 'menu-'.$active->id);
        $inactiveHtml = $this->card($response, 'menu-'.$inactive->id);
        $this->assertStringContainsString('Configured status: Active', $activeHtml);
        $this->assertStringContainsString('bg-emerald-100', $activeHtml);
        $this->assertStringContainsString('Configured status: Inactive', $inactiveHtml);
        $this->assertStringContainsString('bg-slate-200', $inactiveHtml);
        $this->assertStringContainsString('bg-slate-50', $inactiveHtml);
        $this->assertStringContainsString('border-slate-300', $inactiveHtml);
        $this->assertStringContainsString(route('admin.menus.edit', $inactive), $inactiveHtml);
        $this->assertStringContainsString('View items', $inactiveHtml);
        $this->assertStringContainsString('Delete', $inactiveHtml);
        $this->assertStringNotContainsString('disabled', $inactiveHtml);
        $this->patch(route('admin.menus.update', $active), ['name' => $active->name, 'slug' => $active->slug, 'is_active' => 0])->assertSessionHasNoErrors();
        $savedHtml = $this->card($this->get(route('admin.menus.index'))->assertOk(), 'menu-'.$active->id);
        $this->assertStringContainsString('Configured status: Inactive', $savedHtml);
        $this->assertStringNotContainsString('bg-emerald-100', $savedHtml);
    }

    public function test_item_status_badges_and_public_feedback_reflect_persisted_changes(): void
    {
        $this->authorizeMenus();
        $menu = $this->menu(['location' => 'header']);
        $item = $this->item($menu);
        $response = $this->get(route('admin.menus.show', $menu))->assertOk();
        $html = $this->card($response, 'menu-item-'.$item->id);
        $this->assertStringContainsString('Configured status: Active', $html);
        $this->assertStringContainsString('bg-emerald-100', $html);
        $this->assertStringContainsString('Eligible', $html);
        $this->patch(route('admin.menus.items.update', [$menu, $item]), $this->itemData($menu, ['is_active' => 0]))->assertSessionHasNoErrors();
        $html = $this->card($this->get(route('admin.menus.show', $menu))->assertOk(), 'menu-item-'.$item->id);
        $this->assertStringContainsString('Configured status: Inactive', $html);
        $this->assertStringContainsString('bg-slate-200', $html);
        $this->assertStringContainsString('bg-slate-50', $html);
        $this->assertStringContainsString('This menu item is inactive.', $html);
        $this->assertStringContainsString('Hidden', $html);
        $this->assertStringContainsString('Edit', $html);
        $this->assertStringContainsString('Delete', $html);
        $this->assertStringNotContainsString('disabled', $html);
    }

    public function test_active_published_page_reports_unavailable_routing_and_form_clarifies_saved_state(): void
    {
        $this->authorizeMenus();
        $menu = $this->menu(['location' => 'header']);
        $page = Page::create(['title' => 'About Us', 'slug' => 'about-us', 'status' => 'published']);
        $item = $this->item($menu, ['type' => 'page', 'url' => null, 'page_id' => $page->id, 'label' => 'About Us']);
        $response = $this->get(route('admin.menus.show', $menu))->assertOk();
        $html = $this->card($response, 'menu-item-'.$item->id);
        $this->assertStringContainsString('Configured status: Active', $html);
        $this->assertStringContainsString('Public visibility:', $html);
        $this->assertStringContainsString('Hidden', $html);
        $this->assertStringContainsString('Public Page routing is unavailable.', $html);
        $message = 'Published CMS Pages can be assigned to this menu, but their links will not appear on the website until public Page rendering is implemented.';
        $this->get(route('admin.menus.items.create', $menu))->assertOk()->assertSee($message)->assertSee('About Us');
        $this->get(route('admin.menus.items.edit', [$menu, $item]))->assertOk()->assertSee($message)->assertSee('Currently saved configuration')->assertSee('Unsaved form changes are not reflected')->assertSee('Public Page routing is unavailable.');
        $this->get('/')->assertOk()->assertDontSee('About Us');
    }

    public function test_admin_explains_menu_ancestor_page_group_and_url_restrictions(): void
    {
        $this->authorizeMenus();
        $menu = $this->menu(['location' => 'header']);
        $parent = $this->item($menu, ['is_active' => false]);
        $child = $this->item($menu, ['parent_id' => $parent->id]);
        $page = Page::create(['title' => 'Draft', 'slug' => 'draft']);
        $draft = $this->item($menu, ['type' => 'page', 'page_id' => $page->id, 'url' => null]);
        $empty = $this->item($menu, ['type' => 'group', 'url' => null]);
        $invalid = $this->item($menu, ['url' => 'javascript:alert(1)']);
        $response = $this->get(route('admin.menus.show', $menu))->assertOk();
        foreach ([
            $parent->id => 'This menu item is inactive.',
            $child->id => 'A parent item is hidden from public navigation.',
            $draft->id => 'The associated Page is unpublished.',
            $empty->id => 'This navigation group has no publicly eligible children.',
            $invalid->id => 'The destination is invalid or unavailable.',
        ] as $id => $reason) {
            $this->assertStringContainsString($reason, $this->card($response, 'menu-item-'.$id));
        }
        $menu->update(['is_active' => false]);
        $response = $this->get(route('admin.menus.show', $menu))->assertOk();
        $this->assertStringContainsString('The parent menu is inactive.', $this->card($response, 'menu-item-'.$child->id));
        $this->assertSame([], app(MenuResolver::class)->forLocation('header'));
    }

    public function test_unassigned_menu_is_eligible_by_identifier_but_not_automatically_assigned(): void
    {
        $this->authorizeMenus();
        $menu = $this->menu();
        $item = $this->item($menu);
        $this->get(route('admin.menus.index'))->assertOk()->assertSee('Unassigned location.');
        $response = $this->get(route('admin.menus.show', $menu))->assertOk()->assertSee('Unassigned menu location.')->assertSee('rendered explicitly by its identifier');
        $this->assertStringContainsString('Eligible by identifier', $this->card($response, 'menu-item-'.$item->id));
        $resolver = app(MenuResolver::class);
        $this->assertCount(1, $resolver->forSlug($menu->slug));
        $this->assertSame([], $resolver->forLocation('header'));
        $this->assertSame([], $resolver->forLocation('footer'));
        $menu->update(['is_active' => false]);
        $this->assertSame([], $resolver->forSlug($menu->slug));
    }

    public function test_location_frontend_keeps_home_custom_urls_and_groups_and_excludes_hidden_branches(): void
    {
        $menu = $this->menu(['location' => 'header', 'slug' => 'main-navigation']);
        $this->item($menu, ['label' => 'Home', 'url' => '/']);
        $this->item($menu, ['label' => 'Custom destination', 'url' => '/?custom=1']);
        $this->item($menu, ['label' => 'Inactive destination', 'is_active' => false]);
        $page = Page::create(['title' => 'About Us', 'slug' => 'about-us', 'status' => 'published']);
        $hidden = $this->item($menu, ['label' => 'About Us', 'type' => 'page', 'page_id' => $page->id, 'url' => null]);
        $this->item($menu, ['label' => 'Hidden descendant', 'parent_id' => $hidden->id]);
        $group = $this->item($menu, ['label' => 'Useful group', 'type' => 'group', 'url' => null]);
        $this->item($menu, ['label' => 'Nested destination', 'parent_id' => $group->id, 'url' => '/?nested=1']);
        $footer = $this->menu(['location' => 'footer', 'slug' => 'footer-navigation']);
        $this->item($footer, ['label' => 'Footer Home', 'url' => '/']);
        $this->get('/')->assertOk()->assertSee('Home')->assertSee('Custom destination')->assertSee('Useful group')->assertSee('Nested destination')->assertSee('Footer Home')->assertDontSee('About Us')->assertDontSee('Hidden descendant')->assertDontSee('Inactive destination');
        $menu->update(['is_active' => false]);
        $this->get('/')->assertOk()->assertDontSee('Custom destination')->assertDontSee('Useful group')->assertDontSee('Nested destination')->assertSee('Footer Home');
    }

    public function test_visibility_assessment_and_public_nodes_agree_without_additional_queries(): void
    {
        $menu = $this->menu(['location' => 'header']);
        $visible = $this->item($menu, ['label' => 'Visible']);
        $hidden = $this->item($menu, ['label' => 'Hidden', 'is_active' => false]);
        $child = $this->item($menu, ['parent_id' => $hidden->id]);
        $group = $this->item($menu, ['label' => 'Group', 'type' => 'group', 'url' => null]);
        $nested = $this->item($menu, ['label' => 'Nested', 'parent_id' => $group->id]);
        $menu->load('items.page');
        $resolver = app(MenuResolver::class);
        DB::enableQueryLog();
        DB::flushQueryLog();
        $inspection = $resolver->inspect($menu);
        $this->assertSame([], DB::getQueryLog());
        $nodes = $resolver->forLocation('header');
        $queries = count(DB::getQueryLog());
        DB::disableQueryLog();
        $this->assertSame($inspection['nodes'], $nodes);
        $this->assertSame([true, false, false, true, true], array_map(fn ($item) => $inspection['items'][$item->id]['visible'], [$visible, $hidden, $child, $group, $nested]));
        $this->assertLessThanOrEqual(4, $queries);
    }

    public function test_missing_reference_is_reported_without_disabling_foreign_keys_or_querying_destinations(): void
    {
        // A preloaded in-memory model represents an unavailable reference; real FKs stay enabled.
        $menu = (new Menu)->forceFill(['id' => 999, 'is_active' => true]);
        $missing = (new MenuItem)->forceFill(['id' => 998, 'menu_id' => 999, 'parent_id' => null, 'type' => 'page', 'is_active' => true]);
        $missing->setRelation('page', null);
        $menu->setRelation('items', new Collection([$missing]));
        DB::enableQueryLog();
        DB::flushQueryLog();
        $inspection = app(MenuResolver::class)->inspect($menu);
        $queries = DB::getQueryLog();
        DB::disableQueryLog();
        $this->assertSame([], $inspection['nodes']);
        $this->assertSame(['The associated Page is unavailable.'], $inspection['items'][$missing->id]['reasons']);
        $this->assertSame([], $queries);
    }

    private function card(TestResponse $response, string $id): string
    {
        $document = new DOMDocument;
        $previous = libxml_use_internal_errors(true);
        try {
            $document->loadHTML($response->getContent());
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }
        $node = $document->getElementById($id);
        $this->assertNotNull($node);

        return $document->saveHTML($node);
    }
}
