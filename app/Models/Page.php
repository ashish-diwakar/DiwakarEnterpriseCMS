<?php

namespace App\Models;

use App\Enums\PageStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    public const RESERVED_SLUGS = [
        'admin', 'login', 'logout', 'register', 'storage', 'api', 'dashboard', 'profile',
        'forgot-password', 'reset-password', 'verify-email', 'email', 'confirm-password',
        'password', 'up', 'build',
    ];

    protected $fillable = ['title', 'slug', 'status'];

    protected $attributes = ['status' => 'draft'];

    protected function casts(): array
    {
        return ['status' => PageStatus::class];
    }

    public function sections(): HasMany
    {
        return $this->hasMany(PageSection::class)->orderBy('sort_order')->orderBy('id');
    }
}
