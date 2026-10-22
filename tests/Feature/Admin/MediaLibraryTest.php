<?php

namespace Tests\Feature\Admin;

use App\Actions\PageSections\StorePageSection;
use App\Models\Media;
use App\Models\Page;
use App\Models\User;
use App\Support\Rbac;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class MediaLibraryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        Storage::fake('public');
    }

    private function authorizedUser(): User
    {
        $user = User::factory()->create();
        $user->givePermissionTo([Rbac::PERMISSION_ADMIN_ACCESS, Rbac::PERMISSION_MEDIA_MANAGE]);

        return $user;
    }

    private function image(string $extension): UploadedFile
    {
        $image = imagecreatetruecolor(12, 8);
        ob_start();
        match ($extension) {
            'jpg', 'jpeg' => imagejpeg($image),
            'png' => imagepng($image),
            'webp' => imagewebp($image),
        };
        $content = ob_get_clean();
        imagedestroy($image);

        return UploadedFile::fake()->createWithContent('photo.'.$extension, $content);
    }

    private function pdf(): UploadedFile
    {
        return UploadedFile::fake()->createWithContent('brochure.pdf', "%PDF-1.4\n1 0 obj\n<< /Type /Catalog >>\nendobj\n%%EOF");
    }

    private function record(string $name = 'photo.png', string $mime = 'image/png'): Media
    {
        return Media::create([
            'original_name' => $name, 'disk' => 'public', 'path' => 'media/2026/10/'.uniqid().'.png',
            'mime_type' => $mime, 'size' => 42, 'width' => null, 'height' => null,
        ]);
    }

    public function test_guest_and_unauthorized_requests_are_denied(): void
    {
        $media = $this->record();
        foreach (['get', 'post', 'delete'] as $method) {
            $url = $method === 'delete' ? route('admin.media.destroy', $media) : route('admin.media.index');
            $this->$method($url)->assertRedirect(route('login'));
        }
        $this->patch(route('admin.media.update', $media), ['title' => 'Guest'])->assertRedirect(route('login'));
        foreach ([[], [Rbac::PERMISSION_ADMIN_ACCESS], [Rbac::PERMISSION_MEDIA_MANAGE]] as $permissions) {
            $user = User::factory()->create();
            $user->givePermissionTo($permissions);
            $this->actingAs($user)->get(route('admin.media.index'))->assertForbidden();
            $this->post(route('admin.media.store'))->assertForbidden();
            $this->delete(route('admin.media.destroy', $media))->assertForbidden();
            $this->patch(route('admin.media.update', $media), ['title' => 'Unauthorized'])->assertForbidden();
        }
        $this->assertDatabaseHas('media', ['id' => $media->id]);
    }

    public function test_navigation_and_super_admin_bypass(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Rbac::ROLE_ADMIN);
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertDontSee('Media Library');
        $this->actingAs($this->authorizedUser())->get(route('admin.dashboard'))->assertSee('Media Library');
        $super = User::factory()->create();
        $super->assignRole(Rbac::ROLE_SUPER_ADMIN);
        $this->actingAs($super)->get(route('admin.media.index'))->assertOk();
        $this->post(route('admin.media.store'), ['files' => [$this->image('png')]])->assertSessionHas('status');
        $this->delete(route('admin.media.destroy', Media::first()))->assertSessionHas('status');
    }

    public function test_approved_formats_store_trusted_metadata_and_runtime_urls(): void
    {
        $this->actingAs($this->authorizedUser());
        foreach (['jpg', 'jpeg', 'png', 'webp', 'pdf'] as $extension) {
            $file = $extension === 'pdf' ? $this->pdf() : $this->image($extension);
            $size = $file->getSize();
            $mime = $file->getMimeType();
            $this->post(route('admin.media.store'), ['files' => [$file]])->assertSessionHasNoErrors()->assertSessionHas('status');
            $media = Media::latest('id')->first();
            $this->assertSame($file->getClientOriginalName(), $media->original_name);
            $this->assertSame('public', $media->disk);
            $this->assertSame($mime, $media->mime_type);
            $this->assertSame($size, $media->size);
            $this->assertMatchesRegularExpression('#^media/\d{4}/\d{2}/[A-Za-z0-9]{40}\.(jpg|jpeg|png|webp|pdf)$#', $media->path);
            $this->assertNotSame($media->original_name, basename($media->path));
            $this->assertSame($extension === 'pdf' ? null : 12, $media->width);
            $this->assertSame($extension === 'pdf' ? null : 8, $media->height);
            Storage::disk('public')->assertExists($media->path);
            $this->assertSame('/storage/'.$media->path, $media->url());
        }
        $this->assertDatabaseCount('media', 5);
    }

    public function test_invalid_uploads_leave_no_records_or_files(): void
    {
        $this->actingAs($this->authorizedUser());
        $png = $this->image('png');
        $invalid = [
            null,
            UploadedFile::fake()->createWithContent('bad.svg', '<svg xmlns="http://www.w3.org/2000/svg"></svg>'),
            UploadedFile::fake()->createWithContent('bad.php', '<?php echo 1;'),
            UploadedFile::fake()->createWithContent('bad.jpg', '<?php echo 1;'),
            UploadedFile::fake()->createWithContent('bad.html', '<script>alert(1)</script>'),
            UploadedFile::fake()->createWithContent('bad.gif', 'GIF89a'),
            UploadedFile::fake()->createWithContent('wrong.jpg', file_get_contents($png->getRealPath()))->mimeType('image/png'),
            UploadedFile::fake()->createWithContent(str_repeat('a', 256).'.png', file_get_contents($png->getRealPath())),
            $this->image('png')->size(5121),
            $this->pdf()->size(10241),
        ];
        foreach ($invalid as $file) {
            $this->post(route('admin.media.store'), $file ? ['files' => [$file]] : [])->assertSessionHasErrors($file ? 'files.0' : 'files');
            $this->assertDatabaseCount('media', 0);
            $this->assertSame([], Storage::disk('public')->allFiles());
        }
    }

    public function test_original_paths_and_injected_storage_attributes_are_not_trusted(): void
    {
        $file = $this->image('png');
        $upload = UploadedFile::fake()->createWithContent('..\\..\\unsafe.png', file_get_contents($file->getRealPath()));
        $this->actingAs($this->authorizedUser())->post(route('admin.media.store'), [
            'files' => [$upload], 'disk' => 'local', 'path' => '../../evil.php', 'file_name' => 'evil.php',
            'mime_type' => 'text/html', 'size' => 999, 'width' => 999, 'height' => 999,
        ])->assertSessionHasNoErrors()->assertSessionHas('status');
        $media = Media::first();
        $this->assertSame('unsafe.png', $media->original_name);
        $this->assertSame('public', $media->disk);
        $this->assertSame('image/png', $media->mime_type);
        $this->assertSame(12, $media->width);
        $this->assertSame(8, $media->height);
        $this->assertNotSame(999, $media->size);
        $this->assertStringStartsWith('media/'.now()->format('Y/m').'/', $media->path);
    }

    public function test_real_content_detection_rejects_browser_mime_spoofing(): void
    {
        $source = UploadedFile::fake()->createWithContent('source.txt', '<?php echo "unsafe";');
        $upload = new UploadedFile($source->getRealPath(), 'fake.pdf', 'application/pdf', null, true);
        $this->actingAs($this->authorizedUser())->post(route('admin.media.store'), ['files' => [$upload]])->assertSessionHasErrors('files.0');
        $this->assertDatabaseCount('media', 0);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_browsing_order_search_filters_pagination_and_escaping(): void
    {
        $this->actingAs($this->authorizedUser());
        $old = $this->record('old.png');
        $old->forceFill(['created_at' => now()->subDay()])->save();
        $first = $this->record('first.png');
        $second = $this->record('second.png');
        $this->get(route('admin.media.index'))->assertSeeInOrder(['second.png', 'first.png', 'old.png']);
        $pdf = $this->record('certificate.pdf', 'application/pdf');
        $this->get(route('admin.media.index', ['type' => 'documents']))->assertSee('certificate.pdf')->assertDontSee('first.png');
        $this->get(route('admin.media.index', ['type' => 'images']))->assertSee('first.png')->assertDontSee('certificate.pdf');
        $this->get(route('admin.media.index', ['search' => 'certificate', 'type' => 'all']))->assertSee($pdf->original_name)->assertDontSee('first.png');
        $unsafe = $this->record('<script>alert(1)</script>.png');
        $this->get(route('admin.media.index'))->assertSee(e($unsafe->original_name), false)->assertDontSee($unsafe->original_name, false)->assertSee('return confirm(', false)->assertSee('name="_token"', false);
        for ($i = 0; $i < 25; $i++) {
            $this->record('batch-'.$i.'.png');
        }
        $this->get(route('admin.media.index', ['search' => 'batch-', 'type' => 'images']))
            ->assertViewHas('mediaItems', fn ($items) => $items->count() === 24 && $items->total() === 25)
            ->assertSee('search=batch-', false)->assertSee('type=images', false);
        $this->get(route('admin.media.index', ['search' => 'batch-', 'type' => 'images', 'page' => 2]))->assertViewHas('mediaItems', fn ($items) => $items->count() === 1);
        $this->get(route('admin.media.index', ['search' => str_repeat('x', 256)]))->assertSessionHasErrors('search');
        $this->get(route('admin.media.index', ['type' => 'scripts']))->assertSessionHasErrors('type');
    }

    public function test_deletion_and_missing_resources(): void
    {
        $media = $this->record();
        Storage::disk('public')->put($media->path, 'content');
        $unauthorized = User::factory()->create();
        $unauthorized->givePermissionTo(Rbac::PERMISSION_ADMIN_ACCESS);
        $this->actingAs($unauthorized)->delete(route('admin.media.destroy', $media))->assertForbidden();
        Storage::disk('public')->assertExists($media->path);
        $this->actingAs($this->authorizedUser())->delete(route('admin.media.destroy', $media))->assertSessionHas('status');
        Storage::disk('public')->assertMissing($media->path);
        $this->assertModelMissing($media);
        $missingFile = $this->record();
        $this->delete(route('admin.media.destroy', $missingFile))->assertSessionHas('status');
        $this->assertModelMissing($missingFile);
        $this->delete(route('admin.media.destroy', 99999))->assertNotFound();
    }

    public function test_failed_database_insert_cleans_up_the_stored_file(): void
    {
        Media::creating(function (): void {
            throw new RuntimeException('Simulated insert failure');
        });
        try {
            $this->actingAs($this->authorizedUser())->post(route('admin.media.store'), ['files' => [$this->image('png')]])->assertSessionHas('error');
            $this->assertDatabaseCount('media', 0);
            $this->assertSame([], Storage::disk('public')->allFiles());
        } finally {
            Media::flushEventListeners();
        }
    }

    public function test_failed_file_deletion_preserves_record(): void
    {
        $media = $this->record();
        $disk = \Mockery::mock(FilesystemAdapter::class);
        $disk->shouldReceive('exists')->with($media->path)->once()->andReturn(true);
        $disk->shouldReceive('delete')->with($media->path)->once()->andReturn(false);
        Storage::shouldReceive('disk')->with('public')->andReturn($disk);
        $this->actingAs($this->authorizedUser())->delete(route('admin.media.destroy', $media))->assertSessionHas('error');
        $this->assertModelExists($media);
    }

    public function test_database_delete_failure_is_reported_and_retry_cleans_missing_file_record(): void
    {
        $media = $this->record();
        Storage::disk('public')->put($media->path, 'content');
        Media::deleting(function (): void {
            throw new RuntimeException('Simulated delete failure');
        });
        try {
            $this->actingAs($this->authorizedUser())->delete(route('admin.media.destroy', $media))->assertSessionHas('error');
            $this->assertModelExists($media);
            Storage::disk('public')->assertMissing($media->path);
        } finally {
            Media::flushEventListeners();
        }
        $this->delete(route('admin.media.destroy', $media))->assertSessionHas('status');
        $this->assertModelMissing($media);
    }

    public function test_mixed_batch_and_ten_file_limit(): void
    {
        $this->actingAs($this->authorizedUser());
        $this->post(route('admin.media.store'), ['files' => [$this->image('jpg'), $this->image('png'), $this->image('webp'), $this->pdf()]])
            ->assertSessionHasNoErrors()->assertSessionHas('status', '4 media file(s) uploaded successfully.');
        $this->assertDatabaseCount('media', 4);
        $this->assertCount(4, Storage::disk('public')->allFiles());
        $files = [];
        for ($i = 0; $i < 10; $i++) {
            $files[] = $this->image('png');
        }
        $this->post(route('admin.media.store'), ['files' => $files])->assertSessionHasNoErrors()->assertSessionHas('status');
        $this->assertDatabaseCount('media', 14);
        $files[] = $this->pdf();
        $this->post(route('admin.media.store'), ['files' => $files])->assertSessionHasErrors('files');
        $this->assertDatabaseCount('media', 14);
        $this->assertCount(14, Storage::disk('public')->allFiles());
    }

    public function test_complete_batch_is_validated_before_any_storage(): void
    {
        $this->actingAs($this->authorizedUser());
        foreach ([$this->image('png')->size(5121), $this->pdf()->size(10241), UploadedFile::fake()->createWithContent('bad.svg', '<svg/>')] as $invalid) {
            $this->post(route('admin.media.store'), ['files' => [$this->image('jpg'), $invalid]])->assertSessionHasErrors('files.1');
            $this->assertDatabaseCount('media', 0);
            $this->assertSame([], Storage::disk('public')->allFiles());
        }
        $this->post(route('admin.media.store'), ['files' => [$this->image('png')->size(5120), $this->pdf()->size(10240)]])->assertSessionHasNoErrors()->assertSessionHas('status');
        $this->assertDatabaseCount('media', 2);
    }

    public function test_later_batch_failure_keeps_previous_success_and_cleans_failed_upload(): void
    {
        $attempts = 0;
        Media::creating(function () use (&$attempts): void {
            if (++$attempts === 2) {
                throw new RuntimeException('Simulated second insert failure');
            }
        });
        try {
            $this->actingAs($this->authorizedUser())->post(route('admin.media.store'), ['files' => [$this->image('jpg'), $this->pdf(), $this->image('png')]])
                ->assertSessionHas('error', 'Upload stopped after 1 successful file(s). Previously uploaded files remain available. Please retry only the remaining files.');
            $this->assertDatabaseCount('media', 1);
            $this->assertCount(1, Storage::disk('public')->allFiles());
            Storage::disk('public')->assertExists(Media::first()->path);
        } finally {
            Media::flushEventListeners();
        }
    }

    public function test_display_name_can_be_added_changed_and_cleared_without_storage_changes(): void
    {
        $media = $this->record();
        Storage::disk('public')->put($media->path, 'unchanged bytes');
        $before = $media->getAttributes();
        $url = $media->url();
        $this->actingAs($this->authorizedUser());
        foreach (['Friendly name', 'New display name', ''] as $title) {
            $this->patch(route('admin.media.update', $media), [
                'title' => $title, 'original_name' => 'changed.php', 'path' => 'evil.php', 'disk' => 'local',
                'mime_type' => 'text/html', 'size' => 999, 'width' => 999, 'height' => 999, 'id' => 999,
            ])->assertSessionHasNoErrors()->assertSessionHas('status');
            $media->refresh();
            $this->assertSame($title === '' ? null : $title, $media->title);
            foreach (['id', 'original_name', 'disk', 'path', 'mime_type', 'size', 'width', 'height'] as $attribute) {
                $this->assertEquals($before[$attribute], $media->getAttribute($attribute));
            }
            $this->assertSame($url, $media->url());
            $this->get(route('admin.media.index'))->assertSee('<img src="'.e($url).'"', false);
            $this->assertSame('unchanged bytes', Storage::disk('public')->get($media->path));
        }
        $this->get(route('admin.media.index'))->assertSee($media->original_name);
        $this->patch(route('admin.media.update', $media), ['title' => null])->assertSessionHasNoErrors();
        $this->patch(route('admin.media.update', $media), ['title' => str_repeat('x', 255)])->assertSessionHasNoErrors();
        $this->patch(route('admin.media.update', $media), ['title' => str_repeat('x', 256)])->assertSessionHasErrors('title');
        $this->patch(route('admin.media.update', $media), ['title' => ['invalid']])->assertSessionHasErrors('title');
        $this->patch(route('admin.media.update', $media), [])->assertSessionHasErrors('title');
    }

    public function test_approved_images_render_runtime_previews_and_pdf_uses_document_representation(): void
    {
        $images = [];
        foreach (['image/jpeg', 'image/png', 'image/webp'] as $mime) {
            $images[] = $this->record('preview', $mime);
        }
        $pdf = $this->record('document.pdf', 'application/pdf');
        $response = $this->actingAs($this->authorizedUser())->get(route('admin.media.index'))->assertOk();
        foreach ($images as $image) {
            $response->assertSee('<img src="'.e($image->url()).'"', false);
        }
        $response->assertDontSee('<img src="'.e($pdf->url()).'"', false)
            ->assertSee('PDF document')->assertSee('Image preview unavailable.')
            ->assertSee('object-contain')->assertSee('x-on:error="previewFailed = true"', false);
        $this->assertSame(4, substr_count($response->getContent(), 'style="height: 12rem;"'));
        $this->assertSame(4, substr_count($response->getContent(), 'style="align-self: start;"'));
        $this->assertSame(3, substr_count($response->getContent(), 'style="width: 100%; height: 100%; object-fit: contain;"'));
    }

    public function test_local_public_preview_and_open_asset_use_same_origin_despite_configured_host(): void
    {
        config(['filesystems.disks.public.url' => 'http://localhost/storage']);
        Storage::forgetDisk('public');
        $media = $this->record('local.jpg', 'image/jpeg');
        $this->assertSame('/storage/'.$media->path, $media->url());
        $response = $this->actingAs($this->authorizedUser())->get('http://127.0.0.1:8000/admin/media')->assertOk();
        $response->assertSee('<img src="'.e($media->url()).'"', false)
            ->assertSee('href="'.e($media->url()).'"', false)
            ->assertSee('style="height: 12rem;"', false)
            ->assertDontSee('src="http://localhost/storage/', false);
    }

    public function test_json_display_name_update_preserves_identity_and_returns_validation_errors(): void
    {
        $media = $this->record('original.png');
        $before = $media->getAttributes();
        $url = $media->url();
        $this->actingAs($this->authorizedUser());
        $title = '<script>alert("title")</script>';
        $this->patchJson(route('admin.media.update', $media), [
            'title' => $title, 'original_name' => 'evil.php', 'disk' => 'local', 'path' => 'evil.php',
            'mime_type' => 'text/html', 'size' => 999, 'width' => 999, 'height' => 999, 'id' => 999,
        ])->assertOk()->assertExactJson(['title' => $title, 'display_name' => $title]);
        $media->refresh();
        foreach (['id', 'original_name', 'disk', 'path', 'mime_type', 'size', 'width', 'height'] as $attribute) {
            $this->assertEquals($before[$attribute], $media->getAttribute($attribute));
        }
        $this->assertSame($url, $media->url());
        $this->get(route('admin.media.index'))->assertSee(e($title), false)->assertDontSee($title, false)
            ->assertSee('x-on:submit.prevent="save($event.currentTarget)"', false)->assertSee('x-text="displayName"', false);
        $this->patchJson(route('admin.media.update', $media), ['title' => str_repeat('x', 256)])
            ->assertUnprocessable()->assertJsonValidationErrors('title');
        $this->assertSame($title, $media->fresh()->title);
        $this->patchJson(route('admin.media.update', $media), ['title' => ''])->assertOk()
            ->assertExactJson(['title' => null, 'display_name' => 'original.png']);
        $this->assertNull($media->fresh()->title);
        $this->actingAs(User::factory()->create())->patchJson(route('admin.media.update', $media), ['title' => 'Denied'])->assertForbidden();
    }

    public function test_referenced_media_cannot_be_deleted_even_when_section_is_disabled(): void
    {
        $media = $this->record();
        Storage::disk('public')->put($media->path, 'preserve bytes');
        $page = Page::create(['title' => 'Owner', 'slug' => 'owner']);
        $section = app(StorePageSection::class)->handle($page, [
            'type' => 'hero', 'heading' => 'Hero', 'media_id' => $media->id, 'image_alt' => 'Image', 'is_enabled' => false,
        ]);
        $this->actingAs($this->authorizedUser())->delete(route('admin.media.destroy', $media))
            ->assertSessionHas('error', 'This image is used by Page Sections. Remove or replace those references before deleting it.');
        $this->assertModelExists($media);
        $this->assertSame('preserve bytes', Storage::disk('public')->get($media->path));
        $section->delete();
        $this->delete(route('admin.media.destroy', $media))->assertSessionHas('status');
        $this->assertModelMissing($media);
        Storage::disk('public')->assertMissing($media->path);
    }

    public function test_display_names_are_escaped_and_both_names_are_searchable_with_filters(): void
    {
        $media = $this->record('original-unique.png');
        $pdf = $this->record('other.pdf', 'application/pdf');
        $title = '<script>alert("unsafe")</script>';
        $this->actingAs($this->authorizedUser())->patch(route('admin.media.update', $media), ['title' => $title])->assertSessionHasNoErrors();
        $this->assertSame($title, $media->fresh()->title);
        $this->get(route('admin.media.index'))->assertSee(e($title), false)->assertDontSee($title, false)->assertSee('original-unique.png');
        $this->get(route('admin.media.index', ['search' => 'unsafe']))->assertViewHas('mediaItems', fn ($items) => $items->pluck('id')->all() === [$media->id]);
        $this->get(route('admin.media.index', ['search' => 'original-unique']))->assertViewHas('mediaItems', fn ($items) => $items->pluck('id')->all() === [$media->id]);
        $this->get(route('admin.media.index', ['search' => 'unsafe', 'type' => 'documents']))->assertViewHas('mediaItems', fn ($items) => $items->isEmpty());
        $super = User::factory()->create();
        $super->assignRole(Rbac::ROLE_SUPER_ADMIN);
        $this->actingAs($super)->patch(route('admin.media.update', $pdf), ['title' => 'Certificate'])->assertSessionHas('status');
        $this->assertSame('Certificate', $pdf->fresh()->title);
    }
}
