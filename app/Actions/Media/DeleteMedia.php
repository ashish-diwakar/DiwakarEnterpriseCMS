<?php

namespace App\Actions\Media;

use App\Models\Media;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class DeleteMedia
{
    public function handle(Media $media): void
    {
        DB::transaction(function () use ($media): void {
            $media = Media::query()->whereKey($media->id)->lockForUpdate()->firstOrFail();
            if ($media->pageSections()->exists()) {
                throw ValidationException::withMessages(['media' => 'This image is used by Page Sections. Remove or replace those references before deleting it.']);
            }
            $disk = Storage::disk($media->disk);
            if ($disk->exists($media->path) && ! $disk->delete($media->path)) {
                throw new RuntimeException('Media file deletion failed.');
            }
            if (! $media->delete()) {
                throw new RuntimeException('Media record deletion failed.');
            }
        });
    }
}
