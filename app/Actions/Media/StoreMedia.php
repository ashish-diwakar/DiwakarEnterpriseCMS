<?php

namespace App\Actions\Media;

use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;

class StoreMedia
{
    public function handle(UploadedFile $file): Media
    {
        $mime = $file->getMimeType();
        $dimensions = str_starts_with($mime, 'image/') ? $file->dimensions() : null;
        if (str_starts_with($mime, 'image/') && ! $dimensions) {
            throw ValidationException::withMessages(['file' => 'The image dimensions could not be read.']);
        }

        $attributes = [
            'original_name' => basename(str_replace('\\', '/', $file->getClientOriginalName())),
            'disk' => 'public',
            'mime_type' => $mime,
            'size' => $file->getSize(),
            'width' => $dimensions ? $dimensions[0] : null,
            'height' => $dimensions ? $dimensions[1] : null,
        ];
        $path = $file->storeAs('media/'.now()->format('Y/m'), $file->hashName(), 'public');
        if ($path === false) {
            throw new RuntimeException('Media storage failed.');
        }

        try {
            return Media::create([...$attributes, 'path' => $path]);
        } catch (Throwable $exception) {
            try {
                if (! Storage::disk('public')->delete($path)) {
                    report(new RuntimeException('Failed to clean up an unpersisted media upload.'));
                }
            } catch (Throwable $cleanupException) {
                report($cleanupException);
            }
            throw $exception;
        }
    }
}
