<?php

namespace Tests\Feature\Admin;

use App\Actions\PageSections\StorePageSection;
use App\Models\Media;
use App\Models\Page;
use App\Models\User;
use App\Support\Rbac;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PageSectionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        Storage::fake('public');
    }

    private function page(string $slug = 'page'): Page
    {
        return Page::create(['title' => ucfirst($slug), 'slug' => $slug]);
    }

    private function authorizeUser(): User
    {
        $user = User::factory()->create();
        $user->givePermissionTo([Rbac::PERMISSION_ADMIN_ACCESS, Rbac::PERMISSION_PAGES_MANAGE]);
        $this->actingAs($user);

        return $user;
    }

    private function data(string $type = 'text', array $extra = []): array
    {
        return array_replace(['type' => $type, 'heading' => 'Section heading', 'body' => 'Plain text', 'is_enabled' => 1], $extra);
    }

    private function image(string $mime = 'image/jpeg'): Media
    {
        return Media::create(['original_name' => 'image.jpg', 'disk' => 'public', 'path' => 'media/test.jpg', 'mime_type' => $mime, 'size' => 15]);
    }

    public function test_authentication_permissions_and_super_admin(): void
    {
        $page = $this->page();
        $this->get(route('admin.pages.sections.index', $page))->assertRedirect(route('login'));
        $this->post(route('admin.pages.sections.store', $page), $this->data())->assertRedirect(route('login'));
        foreach ([[], [Rbac::PERMISSION_ADMIN_ACCESS], [Rbac::PERMISSION_PAGES_MANAGE]] as $permissions) {
            $user = User::factory()->create();
            $user->givePermissionTo($permissions);
            $this->actingAs($user)->get(route('admin.pages.sections.index', $page))->assertForbidden();
            $this->post(route('admin.pages.sections.store', $page), $this->data())->assertForbidden();
        }
        $super = User::factory()->create();
        $super->assignRole(Rbac::ROLE_SUPER_ADMIN);
        $this->actingAs($super)->post(route('admin.pages.sections.store', $page), $this->data())->assertSessionHasNoErrors()->assertRedirect(route('admin.pages.sections.index', $page));
        $this->get(route('admin.pages.sections.index', $page))->assertOk()->assertSee('Section heading');
    }

    public function test_all_three_types_forms_and_checkbox_false(): void
    {
        $this->authorizeUser();
        $page = $this->page();
        $image = $this->image();
        foreach (['hero', 'text', 'image_text'] as $type) {
            $this->get(route('admin.pages.sections.create', [$page, 'type' => $type]))->assertOk()->assertSee('name="is_enabled" value="0"', false);
            $data = $this->data($type, $type === 'image_text' ? ['media_id' => $image->id, 'image_alt' => 'An image'] : []);
            if ($type === 'hero') {
                unset($data['is_enabled']);
            }
            $this->post(route('admin.pages.sections.store', $page), $data)->assertSessionHasNoErrors()->assertSessionHas('status');
        }
        $this->assertSame([1, 2, 3], $page->sections()->pluck('sort_order')->all());
        $section = $page->sections()->first();
        $this->assertTrue($section->is_enabled);
        $this->get(route('admin.pages.sections.edit', [$page, $section]))->assertOk();
        $data = $this->data('hero', ['is_enabled' => '0']);
        unset($data['type']);
        $this->patch(route('admin.pages.sections.update', [$page, $section]), $data)->assertSessionHasNoErrors()->assertRedirect(route('admin.pages.sections.index', $page));
        $this->assertFalse($section->fresh()->is_enabled);
    }

    public function test_active_type_fields_and_validation_input_remain_consistent(): void
    {
        $this->authorizeUser();
        $page = $this->page();
        $create = route('admin.pages.sections.create', $page);
        $this->get($create)->assertOk()->assertSee('Active section type: Hero')
            ->assertSee('Selection not applied.')->assertSee('Changing the section type will discard')
            ->assertSee('Change section type?')->assertSee('Discard changes')
            ->assertSee('aria-labelledby="change-type-heading"', false)
            ->assertSee('$refs.typeDialog.showModal()', false)
            ->assertSee('$refs.typeForm.submit()', false)
            ->assertDontSee('confirm(', false)
            ->assertSee('name="button_url"', false)->assertSee('Clear image');
        $this->get($create.'?type=image_text')->assertOk()
            ->assertSee('Active section type: Image with Text')
            ->assertSee('name="type" value="image_text"', false)
            ->assertDontSee('name="button_url"', false)->assertDontSee('Clear image')
            ->assertSee('name="image_alt" maxlength="255" required', false)
            ->assertSee('Preview unavailable');
        $this->from($create)->post(route('admin.pages.sections.store', $page), $this->data('image_text', ['heading' => 'Keep this heading']))
            ->assertSessionHasErrors('media_id');
        $this->get($create)->assertOk()->assertSee('Active section type: Image with Text')
            ->assertSee('Keep this heading')->assertDontSee('name="button_url"', false);
    }

    public function test_type_specific_validation_and_unapproved_fields(): void
    {
        $this->authorizeUser();
        $page = $this->page();
        $pdf = $this->image('application/pdf');
        foreach ([
            ['type' => 'unknown'], ['heading' => ''], ['heading' => '<b>unsafe</b>'], ['heading' => str_repeat('x', 201)],
            ['body' => ''], ['body' => str_repeat('x', 10001)], ['media_id' => null], ['button_url' => '/contact'],
            ['page_id' => $page->id], ['sort_order' => 8], ['id' => 7], ['content' => ['anything' => 'value']], ['is_enabled' => 'yes'],
        ] as $invalid) {
            $this->post(route('admin.pages.sections.store', $page), $this->data('text', $invalid))->assertSessionHasErrors();
        }
        foreach ([['body' => str_repeat('x', 501)], ['button_label' => 'Go'], ['button_url' => '/contact'], ['media_id' => 999], ['media_id' => $pdf->id, 'image_alt' => 'PDF']] as $invalid) {
            $this->post(route('admin.pages.sections.store', $page), $this->data('hero', $invalid))->assertSessionHasErrors();
        }
        $this->post(route('admin.pages.sections.store', $page), $this->data('image_text'))->assertSessionHasErrors('media_id');
        $image = $this->image();
        $this->post(route('admin.pages.sections.store', $page), $this->data('image_text', ['media_id' => $image->id]))->assertSessionHasErrors('image_alt');
        $this->post(route('admin.pages.sections.store', $page), $this->data('image_text', ['media_id' => $image->id, 'image_alt' => str_repeat('x', 256)]))->assertSessionHasErrors('image_alt');
        $this->assertDatabaseCount('page_sections', 0);
    }

    public function test_url_rules_reject_unsafe_destinations_and_allow_supported_urls(): void
    {
        $this->authorizeUser();
        $page = $this->page();
        foreach (['javascript:alert(1)', 'data:text/html,x', '//evil.test', '/%2fevil.test', '/%252fevil.test', '/a\\b', 'https://user:pass@example.com', '/%0aevil', '/%255cevil', 'https:/broken'] as $url) {
            $this->post(route('admin.pages.sections.store', $page), $this->data('hero', ['button_label' => 'Go', 'button_url' => $url]))->assertSessionHasErrors('button_url');
        }
        foreach (['/contact', '/about?topic=team#contact', '/team%20photos', 'http://example.com/a', 'https://example.com/team%20photos'] as $url) {
            $this->post(route('admin.pages.sections.store', $page), $this->data('hero', ['button_label' => 'Go', 'button_url' => $url]))->assertSessionHasNoErrors();
        }
    }

    public function test_scoped_ownership_applies_to_every_section_operation(): void
    {
        $this->authorizeUser();
        $page = $this->page();
        $other = $this->page('other');
        $section = app(StorePageSection::class)->handle($page, $this->data());
        foreach ([['get', 'edit'], ['patch', 'update'], ['delete', 'destroy'], ['patch', 'move'], ['patch', 'visibility']] as [$method, $action]) {
            $this->$method(route('admin.pages.sections.'.$action, [$other, $section]), [])->assertNotFound();
        }
        $data = $this->data('hero');
        $this->patch(route('admin.pages.sections.update', [$page, $section]), $data)->assertSessionHasErrors('type');
        unset($data['type']);
        $data['page_id'] = $other->id;
        $this->patch(route('admin.pages.sections.update', [$page, $section]), $data)->assertSessionHasErrors('page_id');
        $this->assertSame($page->id, $section->fresh()->page_id);
        $this->assertSame('text', $section->fresh()->type->value);
    }

    public function test_ordering_boundaries_visibility_and_section_deletion(): void
    {
        $this->authorizeUser();
        $page = $this->page();
        $other = $this->page('other');
        $store = app(StorePageSection::class);
        $first = $store->handle($page, $this->data());
        $second = $store->handle($page, $this->data());
        $last = $store->handle($page, $this->data());
        $unrelated = $store->handle($other, $this->data());
        $this->patch(route('admin.pages.sections.move', [$page, $first]), ['direction' => 'up'])->assertSessionHas('status');
        $this->patch(route('admin.pages.sections.move', [$page, $last]), ['direction' => 'down'])->assertSessionHas('status');
        $this->assertSame([$first->id, $second->id, $last->id], $page->sections()->pluck('id')->all());
        $this->patch(route('admin.pages.sections.move', [$page, $second]), ['direction' => 'up'])->assertSessionHas('status');
        $this->assertSame([$second->id, $first->id, $last->id], $page->sections()->pluck('id')->all());
        $this->patch(route('admin.pages.sections.move', [$page, $second]), ['direction' => 'down'])->assertSessionHas('status');
        $this->patch(route('admin.pages.sections.move', [$page, $first]), ['direction' => 'sideways'])->assertSessionHasErrors('direction');
        foreach ([0, 1, 0] as $enabled) {
            $this->patch(route('admin.pages.sections.visibility', [$page, $first]), ['is_enabled' => $enabled])->assertSessionHas('status');
            $this->assertSame((bool) $enabled, $first->fresh()->is_enabled);
        }
        $this->patch(route('admin.pages.sections.visibility', [$page, $first]), ['is_enabled' => 'invalid'])->assertSessionHasErrors('is_enabled');
        $first->forceFill(['sort_order' => 8])->save();
        $second->forceFill(['sort_order' => 8])->save();
        $this->get(route('admin.pages.sections.index', $page))->assertOk();
        $this->delete(route('admin.pages.sections.destroy', [$page, $last]))->assertSessionHas('status');
        $this->assertSame([1, 2], $page->sections()->pluck('sort_order')->all());
        $this->assertSame(1, $unrelated->fresh()->sort_order);
    }

    public function test_image_replacement_clear_and_section_deletion_preserve_assets(): void
    {
        $this->authorizeUser();
        $page = $this->page();
        $image = $this->image();
        $secondImage = $this->image();
        Storage::disk('public')->put($image->path, 'image bytes');
        $this->post(route('admin.pages.sections.store', $page), $this->data('hero', ['media_id' => $image->id, 'image_alt' => 'First image']))->assertSessionHasNoErrors();
        $section = $page->sections()->first();
        $data = $this->data('hero', ['media_id' => $secondImage->id, 'image_alt' => 'Replacement']);
        unset($data['type']);
        $this->patch(route('admin.pages.sections.update', [$page, $section]), $data)->assertSessionHasNoErrors();
        $this->assertSame($secondImage->id, $section->fresh()->media_id);
        $this->patch(route('admin.pages.sections.update', [$page, $section]), array_replace($data, ['media_id' => null, 'image_alt' => null]))->assertSessionHasNoErrors();
        $this->assertNull($section->fresh()->media_id);
        $this->delete(route('admin.pages.sections.destroy', [$page, $section]))->assertSessionHas('status');
        $this->assertModelExists($image);
        Storage::disk('public')->assertExists($image->path);
    }

    public function test_text_is_escaped_and_validation_preserves_form_input(): void
    {
        $this->authorizeUser();
        $page = $this->page();
        $section = app(StorePageSection::class)->handle($page, $this->data('text', ['heading' => '<script>alert(1)</script>']));
        $this->get(route('admin.pages.sections.index', $page))->assertSee(e($section->heading), false)->assertDontSee($section->heading, false)->assertSee('Delete Section?')->assertSee('x-text="deleteHeading"', false);
        $this->from(route('admin.pages.sections.create', $page))->post(route('admin.pages.sections.store', $page), $this->data('text', ['heading' => 'Retain me', 'body' => '']))
            ->assertRedirect(route('admin.pages.sections.create', $page))->assertSessionHasInput('heading', 'Retain me')->assertSessionHasErrors('body');
    }

    public function test_database_foreign_keys_restrict_direct_parent_deletion(): void
    {
        $page = $this->page();
        $image = $this->image();
        $section = app(StorePageSection::class)->handle($page, $this->data('hero', ['media_id' => $image->id, 'image_alt' => 'Image']));
        foreach ([$page, $image] as $parent) {
            try {
                DB::transaction(fn () => $parent->delete());
                $this->fail('A referenced parent must not be deleted.');
            } catch (QueryException $exception) {
                $this->assertNotEmpty($exception->getMessage());
            }
        }
        $this->assertModelExists($section);
        $this->assertModelExists($page);
        $this->assertModelExists($image);
    }

    public function test_action_rechecks_unavailable_image_before_writing(): void
    {
        $page = $this->page();
        $image = $this->image();
        $id = $image->id;
        $image->delete();
        try {
            app(StorePageSection::class)->handle($page, $this->data('hero', ['media_id' => $id, 'image_alt' => 'Unavailable']));
            $this->fail('A stale image selection must fail.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('media_id', $exception->errors());
        }
        $this->assertDatabaseCount('page_sections', 0);
    }
}
