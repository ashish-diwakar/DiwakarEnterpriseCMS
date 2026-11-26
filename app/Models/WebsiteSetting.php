<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    protected function casts(): array
    {
        return ['contacts_initialized_at' => 'datetime', 'addresses_initialized_at' => 'datetime', 'contacts_version' => 'integer'];
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(WebsiteContact::class)->whereNull('website_address_id')->orderBy('type')->orderBy('sort_order')->orderBy('id');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(WebsiteAddress::class)->orderBy('sort_order')->orderBy('id');
    }

    public function activeAddresses(): Collection
    {
        if ($this->addresses_initialized_at === null) {
            return new Collection;
        }
        $this->loadMissing(['addresses.contacts', 'addresses.websiteSetting:id,addresses_initialized_at']);

        return $this->addresses->where('is_active', true)->values();
    }

    public function primaryAddress(): ?WebsiteAddress
    {
        return $this->activeAddresses()->firstWhere('is_primary', true);
    }

    public function activeEmailContacts(): Collection
    {
        return $this->activeContacts('email');
    }

    public function activePhoneContacts(): Collection
    {
        return $this->activeContacts('phone');
    }

    public function primaryEmail(): ?WebsiteContact
    {
        return $this->activeEmailContacts()->firstWhere('is_primary', true);
    }

    public function primaryPhone(): ?WebsiteContact
    {
        return $this->activePhoneContacts()->firstWhere('is_primary', true);
    }

    private function activeContacts(string $type): Collection
    {
        if ($this->contacts_initialized_at === null) {
            return new Collection;
        }
        $this->loadMissing('contacts');

        return $this->contacts->where('type', $type)->where('is_active', true)->values();
    }

    public static function singleton(): self
    {
        return self::query()->firstOrNew([
            'id' => self::SINGLETON_ID,
        ]);
    }

    /**
     * Persist the fixed identity without exposing primary-key mass assignment.
     * The unique primary key serializes concurrent initialization.
     */
    public static function saveSingleton(array $attributes): self
    {
        $settings = new self;
        $settings->fill($attributes);
        $values = $settings->getAttributes();

        self::query()->upsert(
            [['id' => self::SINGLETON_ID, ...$values]],
            ['id'],
            array_keys($values),
        );

        return self::query()->findOrFail(self::SINGLETON_ID);
    }
}
