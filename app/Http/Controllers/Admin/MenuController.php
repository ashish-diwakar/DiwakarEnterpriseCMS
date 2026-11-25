<?php

namespace App\Http\Controllers\Admin;

use App\Enums\MenuLocation;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMenuRequest;
use App\Models\Menu;
use App\Support\MenuResolver;
use App\Support\MenuTree;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate(['search' => ['nullable', 'string', 'max:200']]);
        $query = Menu::query()->withCount('items');
        if (($filters['search'] ?? '') !== '') {
            $query->where(fn ($query) => $query->where('name', 'like', '%'.$filters['search'].'%')->orWhere('slug', 'like', '%'.$filters['search'].'%'));
        }

        return view('admin.menus.index', ['menus' => $query->orderByDesc('updated_at')->orderByDesc('id')->paginate(20)->withQueryString(), 'filters' => $filters]);
    }

    public function create(): View
    {
        return view('admin.menus.form', ['menu' => new Menu(['is_active' => true]), 'locations' => MenuLocation::cases()]);
    }

    public function store(StoreMenuRequest $request): RedirectResponse
    {
        $menu = $this->persist(new Menu, $request->validated());

        return redirect()->route('admin.menus.show', $menu)->with('status', 'Menu created.');
    }

    public function show(Menu $menu, MenuResolver $resolver): View
    {
        $menu->load('items.page');

        return view('admin.menus.show', [
            'menu' => $menu, 'tree' => MenuTree::flatten($menu->items),
            'visibility' => $resolver->inspect($menu)['items'],
        ]);
    }

    public function edit(Menu $menu): View
    {
        return view('admin.menus.form', ['menu' => $menu, 'locations' => MenuLocation::cases()]);
    }

    public function update(StoreMenuRequest $request, Menu $menu): RedirectResponse
    {
        $this->persist($menu, $request->validated());

        return redirect()->route('admin.menus.show', $menu)->with('status', 'Menu updated.');
    }

    public function destroy(Menu $menu): RedirectResponse
    {
        DB::transaction(function () use ($menu): void {
            $owner = Menu::query()->whereKey($menu->id)->lockForUpdate()->firstOrFail();
            if ($owner->items()->exists()) {
                throw ValidationException::withMessages(['menu' => 'Remove the menu items before deleting this menu.']);
            }
            $owner->deleteOrFail();
        });

        return redirect()->route('admin.menus.index')->with('status', 'Menu deleted.');
    }

    private function persist(Menu $menu, array $attributes): Menu
    {
        try {
            $menu->fill($attributes)->saveOrFail();
        } catch (UniqueConstraintViolationException $exception) {
            foreach (['slug', 'location'] as $field) {
                if ($menu->$field !== null && Menu::query()->where($field, $menu->$field)->when($menu->exists, fn ($q) => $q->whereKeyNot($menu->id))->exists()) {
                    throw ValidationException::withMessages([$field => 'This identifier or location is already assigned. Resolve the existing assignment first.']);
                }
            }
            throw $exception;
        }

        return $menu;
    }
}
