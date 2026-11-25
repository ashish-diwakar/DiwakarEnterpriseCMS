<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Menus\MutateMenuItems;
use App\Enums\MenuItemType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MutateMenuItemRequest;
use App\Http\Requests\Admin\StoreMenuItemRequest;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use App\Support\MenuResolver;
use App\Support\MenuTree;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MenuItemController extends Controller
{
    public function create(Menu $menu, MenuResolver $resolver): View
    {
        return $this->form($menu, new MenuItem(['type' => 'url', 'is_active' => true, 'target' => '_self']), $resolver);
    }

    public function edit(Menu $menu, MenuItem $item, MenuResolver $resolver): View
    {
        return $this->form($menu, $item, $resolver);
    }

    public function store(StoreMenuItemRequest $request, Menu $menu, MutateMenuItems $action): RedirectResponse
    {
        $action->save($menu, null, $request->validated());

        return $this->done($menu, 'Menu item added.');
    }

    public function update(StoreMenuItemRequest $request, Menu $menu, MenuItem $item, MutateMenuItems $action): RedirectResponse
    {
        $action->save($menu, $item, $request->validated());

        return $this->done($menu, 'Menu item updated.');
    }

    public function destroy(MutateMenuItemRequest $request, Menu $menu, MenuItem $item, MutateMenuItems $action): RedirectResponse
    {
        $action->delete($menu, $item, (int) $request->validated('version'));

        return $this->done($menu, 'Menu item deleted.');
    }

    public function move(MutateMenuItemRequest $request, Menu $menu, MenuItem $item, MutateMenuItems $action): RedirectResponse
    {
        $action->move($menu, $item, (int) $request->validated('version'), $request->validated('direction'));

        return $this->done($menu, 'Menu item order updated.');
    }

    public function reorder(MutateMenuItemRequest $request, Menu $menu, MutateMenuItems $action): RedirectResponse
    {
        $data = $request->validated();
        $action->reorder($menu, isset($data['parent_id']) ? (int) $data['parent_id'] : null, $data['items'], (int) $data['version']);

        return $this->done($menu, 'Menu item order updated.');
    }

    private function form(Menu $menu, MenuItem $item, MenuResolver $resolver): View
    {
        $menu->load('items.page');

        return view('admin.menus.item-form', [
            'menu' => $menu, 'item' => $item, 'types' => MenuItemType::cases(),
            'parents' => MenuTree::flatten($menu->items),
            'visibility' => $item->exists ? $resolver->inspect($menu)['items'][$item->id] : null,
            'pages' => Page::query()->orderBy('title')->get(['id', 'title', 'status']),
        ]);
    }

    private function done(Menu $menu, string $message): RedirectResponse
    {
        return redirect()->route('admin.menus.show', $menu)->with('status', $message);
    }
}
