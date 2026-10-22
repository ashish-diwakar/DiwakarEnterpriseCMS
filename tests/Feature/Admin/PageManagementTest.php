<?php

namespace Tests\Feature\Admin;

use App\Actions\PageSections\DeletePageSection;
use App\Actions\PageSections\StorePageSection;
use App\Enums\PageStatus;
use App\Models\Page;
use App\Models\User;
use App\Support\Rbac;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class PageManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function authorizedUser(): User
    {
        $user = User::factory()->create();
        $user->givePermissionTo([Rbac::PERMISSION_ADMIN_ACCESS, Rbac::PERMISSION_PAGES_MANAGE]);

        return $user;
    }

    private function page(string $slug = 'sample', string $status = 'draft', ?string $title = null): Page
    {
        return Page::create(['title' => $title ?? ucfirst($slug), 'slug' => $slug, 'status' => $status]);
    }

    public function test_all_routes_require_both_permissions_and_guests_are_redirected(): void
    {
        $page = $this->page();
        $routes = [
            ['get', route('admin.pages.index')], ['get', route('admin.pages.create')],
            ['post', route('admin.pages.store')], ['get', route('admin.pages.edit', $page)],
            ['patch', route('admin.pages.update', $page)], ['delete', route('admin.pages.destroy', $page)],
        ];
        foreach ($routes as [$method, $url]) {
            $this->$method($url)->assertRedirect(route('login'));
        }
        foreach ([[], [Rbac::PERMISSION_ADMIN_ACCESS], [Rbac::PERMISSION_PAGES_MANAGE]] as $permissions) {
            $user = User::factory()->create();
            $user->givePermissionTo($permissions);
            $this->actingAs($user);
            foreach ($routes as [$method, $url]) {
                $this->$method($url)->assertForbidden();
            }
        }
        $this->assertDatabaseCount('pages', 1);
    }

    public function test_authorized_and_super_admin_access_and_navigation(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Rbac::ROLE_ADMIN);
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertDontSee('href="'.route('admin.pages.index').'"', false);
        foreach ([$this->authorizedUser(), User::factory()->create()->assignRole(Rbac::ROLE_SUPER_ADMIN)] as $user) {
            $this->actingAs($user)->get(route('admin.pages.index'))->assertOk()->assertSee('Pages');
            $this->get(route('admin.pages.create'))->assertOk()->assertSee('x-data="pageSlug"', false);
            $this->post(route('admin.pages.store'), ['title' => 'Allowed'])->assertSessionHasNoErrors()->assertSessionHas('status');
            $page = Page::first();
            $this->get(route('admin.pages.edit', $page))->assertOk()->assertDontSee('x-data="pageSlug"', false);
            $this->patch(route('admin.pages.update', $page), ['status' => 'published'])->assertSessionHas('status');
            $this->delete(route('admin.pages.destroy', $page))->assertSessionHas('status');
        }
    }

    public function test_creation_defaults_slug_normalization_and_approved_fields_only(): void
    {
        $this->actingAs($this->authorizedUser());
        $this->post(route('admin.pages.store'), [
            'title' => '  Café & Company  ', 'slug' => '', 'id' => 999, 'body' => '<p>Content</p>',
            'media_id' => 42, 'parent_id' => 42, 'is_homepage' => true, 'meta_title' => 'SEO',
        ])->assertSessionHasNoErrors()->assertSessionHas('status');
        $page = Page::first();
        $this->assertSame('Café & Company', $page->title);
        $this->assertSame('cafe-company', $page->slug);
        $this->assertSame(PageStatus::Draft, $page->status);
        $this->assertNotSame(999, $page->id);
        $this->assertSame(['title', 'slug', 'status'], $page->getFillable());
        foreach (['About Us' => 'about-us', 'About_US' => 'about-us', ' Contact ' => 'contact', 'ABOUT' => 'about'] as $input => $expected) {
            $this->post(route('admin.pages.store'), ['title' => 'Explicit', 'slug' => $input, 'status' => 'published'])->assertSessionHasNoErrors();
            $created = Page::where('slug', $expected)->firstOrFail();
            $this->assertSame(PageStatus::Published, $created->status);
            $created->delete();
        }
    }

    public function test_title_validation_and_invalid_statuses(): void
    {
        $this->actingAs($this->authorizedUser());
        foreach ([null, '', '   ', str_repeat('x', 201), '<b>Title</b>', ['array']] as $title) {
            $this->post(route('admin.pages.store'), ['title' => $title, 'slug' => 'valid'])->assertSessionHasErrors('title');
        }
        foreach (['', null, 'scheduled', ['draft']] as $status) {
            $this->post(route('admin.pages.store'), ['title' => 'Valid', 'status' => $status])->assertSessionHasErrors('status');
        }
        $this->assertDatabaseCount('pages', 0);
        $this->post(route('admin.pages.store'), ['title' => str_repeat('x', 200), 'slug' => 'valid'])->assertSessionHasNoErrors();
    }

    public function test_unsafe_reserved_empty_duplicate_and_overlong_slugs_are_rejected(): void
    {
        $this->actingAs($this->authorizedUser());
        $this->page('existing');
        foreach (['about///team', 'a\\b', '../team', 'https://example.com', 'about?x=1', 'about#fragment', '<b>about</b>', '---', str_repeat('a', 151), ['bad'], 'EXISTING'] as $slug) {
            $this->post(route('admin.pages.store'), ['title' => 'Valid', 'slug' => $slug])->assertSessionHasErrors('slug');
        }
        foreach (Page::RESERVED_SLUGS as $slug) {
            $this->post(route('admin.pages.store'), ['title' => 'Valid', 'slug' => strtoupper(str_replace('-', ' ', $slug))])->assertSessionHasErrors('slug');
        }
        $this->post(route('admin.pages.store'), ['title' => '!!!'])->assertSessionHasErrors('slug');
        $this->post(route('admin.pages.store'), ['title' => str_repeat('a', 151)])->assertSessionHasErrors('slug');
        $this->assertDatabaseCount('pages', 1);
        $this->post(route('admin.pages.store'), ['title' => 'Maximum slug', 'slug' => str_repeat('a', 150)])->assertSessionHasNoErrors();
    }

    public function test_updates_preserve_omitted_fields_and_allow_intentional_changes(): void
    {
        $page = $this->page('stable', 'published');
        $other = $this->page('taken');
        $this->actingAs($this->authorizedUser());
        $this->patch(route('admin.pages.update', $page), ['title' => 'Renamed', 'id' => 999, 'body' => 'ignored'])->assertSessionHasNoErrors();
        $page->refresh();
        $this->assertSame('Renamed', $page->title);
        $this->assertSame('stable', $page->slug);
        $this->assertSame(PageStatus::Published, $page->status);
        $this->patch(route('admin.pages.update', $page), ['slug' => 'stable'])->assertSessionHasNoErrors();
        $this->patch(route('admin.pages.update', $page), ['slug' => ' New_Name ', 'status' => 'draft'])->assertSessionHasNoErrors();
        $this->assertSame('new-name', $page->fresh()->slug);
        $this->assertSame(PageStatus::Draft, $page->fresh()->status);
        foreach ([['slug' => ''], ['slug' => null], ['slug' => $other->slug], ['title' => ''], ['status' => ''], ['status' => null], ['status' => 'invalid']] as $data) {
            $this->patch(route('admin.pages.update', $page), $data)->assertSessionHasErrors(array_key_first($data));
        }
        $this->assertSame('new-name', $page->fresh()->slug);
        $this->assertSame('Renamed', $page->fresh()->title);
    }

    public function test_search_filters_order_and_pagination(): void
    {
        $old = $this->page('alpha', 'draft', 'Old title');
        $old->forceFill(['updated_at' => now()->subDay()])->save();
        $first = $this->page('bravo', 'draft', 'First title');
        $second = $this->page('charlie', 'published', 'Second title');
        $this->actingAs($this->authorizedUser())->get(route('admin.pages.index'))->assertSeeInOrder(['Second title', 'First title', 'Old title']);
        $this->get(route('admin.pages.index', ['search' => 'First']))->assertViewHas('pages', fn ($items) => $items->pluck('id')->all() === [$first->id]);
        $this->get(route('admin.pages.index', ['search' => 'charlie']))->assertViewHas('pages', fn ($items) => $items->pluck('id')->all() === [$second->id]);
        $this->get(route('admin.pages.index', ['status' => 'published']))->assertViewHas('pages', fn ($items) => $items->pluck('id')->all() === [$second->id]);
        $this->get(route('admin.pages.index', ['search' => 'Second', 'status' => 'draft']))->assertViewHas('pages', fn ($items) => $items->isEmpty());
        for ($i = 0; $i < 21; $i++) {
            $this->page('batch-'.$i);
        }
        $this->get(route('admin.pages.index', ['search' => 'batch-', 'status' => 'draft']))->assertViewHas('pages', fn ($items) => $items->count() === 20 && $items->total() === 21)->assertSee('search=batch-', false)->assertSee('status=draft', false);
        $this->get(route('admin.pages.index', ['search' => 'batch-', 'status' => 'draft', 'page' => 2]))->assertViewHas('pages', fn ($items) => $items->count() === 1);
        $this->get(route('admin.pages.index', ['search' => str_repeat('x', 201)]))->assertSessionHasErrors('search');
        $this->get(route('admin.pages.index', ['status' => 'invalid']))->assertSessionHasErrors('status');
    }

    public function test_escaping_confirmation_deletion_and_no_public_route(): void
    {
        $page = $this->page('safe-slug', 'draft', '<script>alert("unsafe")</script>');
        $this->actingAs($this->authorizedUser());
        $response = $this->get(route('admin.pages.index'))->assertSee(e($page->title), false)->assertDontSee($page->title, false)
            ->assertSee('data-page-title="'.e($page->title).'"', false)
            ->assertSee('data-delete-action="'.route('admin.pages.destroy', $page).'"', false)
            ->assertSee('x-text="deleteTitle"', false)->assertSee('Delete Page?')
            ->assertSee('type="button" autofocus', false)->assertSee('$refs.deleteDialog.close()', false)
            ->assertSee('name="_method" value="DELETE"', false)->assertSee('name="_token"', false)
            ->assertDontSee('return confirm(', false);
        $this->assertSame(1, substr_count($response->getContent(), '<dialog '));
        $this->get(route('admin.pages.edit', $page))->assertSee(e($page->title), false)->assertDontSee($page->title, false);
        $form = $this->get(route('admin.pages.create'))->assertOk();
        foreach (['body', 'content', 'media_id', 'parent_id', 'meta_title', 'show_in_menu'] as $field) {
            $form->assertDontSee('name="'.$field.'"', false);
        }
        $this->delete(route('admin.pages.destroy', $page))->assertRedirect(route('admin.pages.index'));
        $this->assertModelMissing($page);
        $this->get(route('admin.pages.edit', 99999))->assertNotFound();
        $this->delete(route('admin.pages.destroy', 99999))->assertNotFound();
        $this->get('/safe-slug')->assertNotFound();
        $this->assertFalse(collect(Route::getRoutes())->contains(fn ($route) => $route->uri() === '{slug}'));
    }

    public function test_database_slug_race_returns_a_validation_error(): void
    {
        $this->page('occupied');
        Page::creating(function (Page $page): void {
            $page->slug = 'occupied';
        });
        try {
            $this->actingAs($this->authorizedUser())->post(route('admin.pages.store'), ['title' => 'New', 'slug' => 'available'])->assertSessionHasErrors('slug');
            $this->assertDatabaseCount('pages', 1);
        } finally {
            Page::flushEventListeners();
        }
    }

    public function test_populated_page_deletion_is_blocked_including_disabled_sections(): void
    {
        $page = $this->page();
        $section = app(StorePageSection::class)->handle($page, ['type' => 'text', 'heading' => 'Content', 'body' => 'Preserve content', 'is_enabled' => false]);
        $this->actingAs($this->authorizedUser())->delete(route('admin.pages.destroy', $page))
            ->assertSessionHas('error', 'This Page contains sections. Remove its sections before deleting the Page.');
        $this->assertModelExists($page);
        $this->assertModelExists($section);
        app(DeletePageSection::class)->handle($page, $section);
        $this->delete(route('admin.pages.destroy', $page))->assertSessionHas('status');
        $this->assertModelMissing($page);
    }

    public function test_successful_saves_return_to_index_and_validation_retains_form_context(): void
    {
        $this->actingAs($this->authorizedUser())->post(route('admin.pages.store'), ['title' => 'Saved page'])
            ->assertRedirect(route('admin.pages.index'))->assertSessionHas('status', 'Page created successfully.');
        $page = Page::firstOrFail();
        $this->patch(route('admin.pages.update', $page), ['title' => 'Updated page'])
            ->assertRedirect(route('admin.pages.index'))->assertSessionHas('status', 'Page updated successfully.');
        $this->assertSame('saved-page', $page->fresh()->slug);
        $this->from(route('admin.pages.create'))->post(route('admin.pages.store'), ['title' => 'Retained title', 'slug' => 'admin'])
            ->assertRedirect(route('admin.pages.create'))->assertSessionHasErrors('slug')->assertSessionHasInput('title', 'Retained title');
        $this->from(route('admin.pages.edit', $page))->patch(route('admin.pages.update', $page), ['title' => 'Retained edit', 'slug' => ''])
            ->assertRedirect(route('admin.pages.edit', $page))->assertSessionHasErrors('slug')->assertSessionHasInput('title', 'Retained edit');
        $this->assertSame('Updated page', $page->fresh()->title);
        $this->assertDatabaseCount('pages', 1);
    }
}
