<?php

namespace App\Actions\Media;

use App\Models\Media;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class DeleteMedia
{
    public function handle(Media $media): void
    {
        $disk = Storage::disk($media->disk);
        if ($disk->exists($media->path) && ! $disk->delete($media->path)) {
            throw new RuntimeException('Media file deletion failed.');
        }
        if (! $media->delete()) {
            throw new RuntimeException('Media record deletion failed.');
        }
    }
}
