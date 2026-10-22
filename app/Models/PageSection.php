<?php

namespace App\Models;

use App\Enums\PageSectionType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

class PageSection extends Model
{
    public const IMAGE_MIMES = ['image/jpeg', 'image/png', 'image/webp'];

    protected $fillable = ['type', 'is_enabled', 'heading', 'body', 'media_id', 'image_alt', 'button_label', 'button_url'];

    protected function casts(): array
    {
        return ['type' => PageSectionType::class, 'is_enabled' => 'boolean', 'sort_order' => 'integer'];
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }

    // Call within a transaction after locking the owning Page row.
    public static function normalizeOrdering(Page $page): Collection
    {
        $sections = $page->sections()->lockForUpdate()->get();
        foreach ($sections as $index => $section) {
            if ($section->sort_order !== $index + 1) {
                $section->forceFill(['sort_order' => $index + 1])->saveOrFail();
            }
        }

        return $sections;
    }

    // Coordinate assignment with DeleteMedia before changing a reference.
    public static function lockImageReference(?int $id): void
    {
        if ($id === null) {
            return;
        }
        $image = Media::query()->whereKey($id)->lockForUpdate()->first();
        if (! $image || ! in_array($image->mime_type, self::IMAGE_MIMES, true)) {
            throw ValidationException::withMessages(['media_id' => 'The selected image is unavailable. Please select another image.']);
        }
    }
}
