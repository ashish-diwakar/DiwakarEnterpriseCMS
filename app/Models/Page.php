<?php

namespace App\Models;

use App\Enums\PageStatus;
use Illuminate\Database\Eloquent\Model;

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
}
