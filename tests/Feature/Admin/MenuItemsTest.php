<?php

namespace Tests\Feature\Admin;

use App\Models\MenuItem;
use App\Models\Page;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Tests\Concerns\WithMenuFixtures;
use Tests\TestCase;

class MenuItemsTest extends TestCase
{
    use WithMenuFixtures;

    public function test_all_types_and_type_changes_remove_incompatible_values(): void
    {
        $this->authorizeMenus();
        $menu = $this->menu();
        $page = Page::create(['title' => 'Draft page', 'slug' => 'draft']);
        $this->get(route('admin.menus.items.create', $menu))->assertOk()->assertSee('Draft page')->assertSee('Draft');
        $this->post(route('admin.menus.items.store', $menu), $this->itemData($menu, ['type' => 'page', 'page_id' => $page->id, 'url' => 'javascript:ignored', 'menu_id' => 999, 'sort_order' => 999]))->assertSessionHasNoErrors();
        $item = MenuItem::first();
        $this->assertSame($page->id, $item->page_id);
        $this->assertNull($item->url);
        $this->assertSame($menu->id, $item->menu_id);
        $this->assertSame(1, $item->sort_order);
        $this->patch(route('admin.menus.items.update', [$menu, $item]), $this->itemData($menu, ['type' => 'url', 'url' => 'https://example.com/a?q=b#c', 'page_id' => 999]))->assertSessionHasNoErrors();
        $this->assertNull($item->fresh()->page_id);
        $this->patch(route('admin.menus.items.update', [$menu, $item]), $this->itemData($menu, ['type' => 'group', 'target' => '_blank']))->assertSessionHasNoErrors();
        $this->assertDatabaseHas('menu_items', ['id' => $item->id, 'url' => null, 'page_id' => null, 'target' => '_self', 'type' => 'group']);
        $this->get(route('admin.menus.items.edit', [$menu, $item]))->assertOk()->assertSee('Navigation group');
    }

    public function test_type_specific_fields_and_target_are_validated(): void
    {
        $this->authorizeMenus();
        $menu = $this->menu();
        foreach ([['type' => 'page', 'page_id' => 999], ['type' => 'url', 'url' => null], ['type' => 'unknown'], ['target' => 'arbitrary'], ['label' => str_repeat('a', 201)], ['parent_id' => 999]] as $invalid) {
            $this->post(route('admin.menus.items.store', $menu), $this->itemData($menu, $invalid))->assertSessionHasErrors();
        }
        $this->assertDatabaseCount('menu_items', 0);
    }

    public function test_cross_menu_parent_and_resource_binding_rejected(): void
    {
        $this->authorizeMenus();
        $menu = $this->menu();
        $other = $this->menu();
        $foreign = $this->item($other);
        $this->post(route('admin.menus.items.store', $menu), $this->itemData($menu, ['parent_id' => $foreign->id]))->assertSessionHasErrors('parent_id');
        foreach (['edit' => 'get', 'update' => 'patch', 'destroy' => 'delete', 'move' => 'patch'] as $route => $method) {
            $this->$method(route('admin.menus.items.'.$route, [$menu, $foreign]), $method === 'get' ? [] : $this->itemData($menu))->assertNotFound();
        }
        $this->assertSame($other->id, $foreign->fresh()->menu_id);
    }

    public function test_hierarchy_depth_cycles_and_whole_subtree_moves(): void
    {
        $this->authorizeMenus();
        $menu = $this->menu();
        $root = $this->item($menu);
        $child = $this->item($menu, ['parent_id' => $root->id, 'sort_order' => 1]);
        $leaf = $this->item($menu, ['parent_id' => $child->id, 'sort_order' => 1]);
        $other = $this->item($menu);
        $otherChild = $this->item($menu, ['parent_id' => $other->id, 'sort_order' => 1]);
        foreach ([$root->id, $child->id, $leaf->id] as $parentId) {
            $this->patch(route('admin.menus.items.update', [$menu, $root]), $this->itemData($menu, ['parent_id' => $parentId]))->assertSessionHasErrors('parent_id');
        }
        $this->post(route('admin.menus.items.store', $menu), $this->itemData($menu, ['parent_id' => $leaf->id]))->assertSessionHasErrors('parent_id');
        $this->patch(route('admin.menus.items.update', [$menu, $child]), $this->itemData($menu, ['parent_id' => $otherChild->id]))->assertSessionHasErrors('parent_id');
        $this->patch(route('admin.menus.items.update', [$menu, $child]), $this->itemData($menu, ['parent_id' => $other->id]))->assertSessionHasNoErrors();
        $this->assertSame($child->id, $leaf->fresh()->parent_id);
        $this->assertSame($other->id, $child->fresh()->parent_id);
        $this->assertSame([1, 2], $menu->items()->where('parent_id', $other->id)->pluck('sort_order')->all());
    }

    public function test_move_and_reorder_preserve_sibling_order_and_reject_stale_versions(): void
    {
        $this->authorizeMenus();
        $menu = $this->menu();
        $one = $this->item($menu);
        $two = $this->item($menu);
        $child = $this->item($menu, ['parent_id' => $one->id, 'sort_order' => 1]);
        $this->patch(route('admin.menus.items.move', [$menu, $two]), ['version' => 0, 'direction' => 'up'])->assertSessionHasNoErrors();
        $this->assertSame([$two->id, $one->id], $menu->items()->whereNull('parent_id')->pluck('id')->all());
        $this->patch(route('admin.menus.items.move', [$menu, $one]), ['version' => 0, 'direction' => 'up'])->assertSessionHasErrors('items');
        $this->patch(route('admin.menus.items.reorder', $menu), ['version' => 1, 'items' => [$one->id, $two->id]])->assertSessionHasNoErrors();
        $this->assertSame([$one->id, $two->id], $menu->items()->whereNull('parent_id')->pluck('id')->all());
        $this->assertSame(1, $child->fresh()->sort_order);
    }

    public function test_reorder_rejects_missing_duplicate_foreign_and_other_sibling_ids_atomically(): void
    {
        $this->authorizeMenus();
        $menu = $this->menu();
        $one = $this->item($menu);
        $two = $this->item($menu);
        $child = $this->item($menu, ['parent_id' => $one->id, 'sort_order' => 1]);
        $foreign = $this->item($this->menu());
        foreach ([[], [$one->id], [$one->id, $one->id], [$one->id, $foreign->id], [$one->id, $child->id]] as $ids) {
            $this->patch(route('admin.menus.items.reorder', $menu), ['version' => 0, 'items' => $ids])->assertSessionHasErrors();
            $this->assertSame(0, $menu->fresh()->lock_version);
            $this->assertSame([$one->id, $two->id], $menu->items()->whereNull('parent_id')->pluck('id')->all());
        }
        $this->patch(route('admin.menus.items.reorder', $menu), ['version' => 0, 'parent_id' => $foreign->id, 'items' => []])->assertSessionHasErrors('items');
    }

    public function test_disabled_references_protect_pages_and_parent_and_menu_deletion(): void
    {
        $user = $this->authorizeMenus();
        $user->givePermissionTo('pages.manage');
        $menu = $this->menu(['is_active' => false]);
        $page = Page::create(['title' => 'Referenced', 'slug' => 'referenced']);
        $parent = $this->item($menu, ['type' => 'page', 'page_id' => $page->id, 'url' => null, 'is_active' => false]);
        $child = $this->item($menu, ['parent_id' => $parent->id, 'sort_order' => 1]);
        $this->delete(route('admin.pages.destroy', $page))->assertSessionHasErrors('page');
        $this->delete(route('admin.menus.items.destroy', [$menu, $parent]), ['version' => 0])->assertSessionHasErrors('items');
        $this->delete(route('admin.menus.items.destroy', [$menu, $child]), ['version' => 0])->assertSessionHasNoErrors();
        $this->delete(route('admin.menus.items.destroy', [$menu, $parent]), ['version' => 1])->assertSessionHasNoErrors();
        $this->delete(route('admin.pages.destroy', $page))->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('pages', ['id' => $page->id]);
    }

    public function test_database_same_menu_parent_constraint(): void
    {
        $a = $this->menu();
        $b = $this->menu();
        $parent = $this->item($a);
        $this->expectException(QueryException::class);
        $this->item($b, ['parent_id' => $parent->id]);
    }

    public function test_database_protects_parent_page_and_menu_without_application_checks(): void
    {
        $menu = $this->menu();
        $page = Page::create(['title' => 'Page', 'slug' => 'page']);
        $parent = $this->item($menu, ['page_id' => $page->id, 'type' => 'page', 'url' => null]);
        $this->item($menu, ['parent_id' => $parent->id]);
        foreach ([['menus', $menu->id], ['pages', $page->id], ['menu_items', $parent->id]] as [$table, $id]) {
            try {
                DB::table($table)->where('id', $id)->delete();
                $this->fail('A restrictive foreign key was not enforced.');
            } catch (QueryException $exception) {
                $this->assertStringContainsString('FOREIGN KEY', $exception->getMessage());
            }
        }
    }

    public function test_failed_multi_write_reorder_rolls_back_every_change(): void
    {
        $this->authorizeMenus();
        $menu = $this->menu();
        $one = $this->item($menu);
        $two = $this->item($menu);
        $updates = 0;
        DB::listen(function ($query) use (&$updates): void {
            if (str_starts_with($query->sql, 'update "menu_items"') && ++$updates === 2) {
                throw new \RuntimeException('Synthetic second-write failure');
            }
        });
        $this->patch(route('admin.menus.items.reorder', $menu), ['version' => 0, 'items' => [$two->id, $one->id]])->assertServerError();
        $this->assertSame([$one->id, $two->id], $menu->items()->whereNull('parent_id')->pluck('id')->all());
        $this->assertSame(0, $menu->fresh()->lock_version);
    }
}
