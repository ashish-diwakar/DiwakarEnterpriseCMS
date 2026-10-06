<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebsiteSetting extends Model
{
    public const SINGLETON_ID = 1;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'site_name',
        'tagline',
        'public_email',
        'primary_phone',
        'secondary_phone',
        'address',
        'facebook_url',
        'instagram_url',
        'linkedin_url',
        'youtube_url',
        'x_twitter_url',
        'footer_text',
    ];

    public static function singleton(): self
    {
        return self::query()->firstOrNew([
            'id' => self::SINGLETON_ID,
        ]);
    }
}
