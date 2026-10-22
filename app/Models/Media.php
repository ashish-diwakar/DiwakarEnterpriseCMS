<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    protected $fillable = ['original_name', 'disk', 'path', 'mime_type', 'size', 'width', 'height', 'title'];

    protected function casts(): array
    {
        return ['size' => 'integer', 'width' => 'integer', 'height' => 'integer'];
    }

    public function url(): string
    {
        $url = Storage::disk($this->disk)->url($this->path);

        // Local public assets are served by the current site, including its port.
        if ($this->disk === 'public' && config('filesystems.disks.public.driver') === 'local') {
            return parse_url($url, PHP_URL_PATH) ?: $url;
        }

        return $url;
    }

    public function pageSections(): HasMany
    {
        return $this->hasMany(PageSection::class);
    }
}
