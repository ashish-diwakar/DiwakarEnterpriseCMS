<?php

namespace App\Models;

use App\Support\AddressValue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WebsiteAddress extends Model
{
    protected $fillable = ['label', 'address_line_1', 'address_line_2', 'city', 'state', 'postal_code', 'country', 'is_primary', 'is_active'];

    protected $attributes = ['is_primary' => false, 'is_active' => true];

    protected function casts(): array
    {
        return ['is_primary' => 'boolean', 'is_active' => 'boolean', 'sort_order' => 'integer'];
    }

    public function websiteSetting(): BelongsTo
    {
        return $this->belongsTo(WebsiteSetting::class);
    }

    public function contacts(): HasMany
    {
        // The composite foreign key enforces the settings owner. Keep this relation
        // independent of an instance value so eager loading uses the proper keys.
        return $this->hasMany(WebsiteContact::class)->orderBy('type')->orderBy('sort_order')->orderBy('id');
    }

    public function publiclyAvailable(): bool
    {
        return $this->is_active && $this->websiteSetting?->addresses_initialized_at !== null;
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
        if (! $this->publiclyAvailable()) {
            return new Collection;
        }
        $this->loadMissing('contacts');
        foreach ($this->contacts as $contact) {
            $contact->setRelation('websiteAddress', $this);
        }

        return $this->contacts->where('type', $type)->where('is_active', true)->values();
    }

    public function formattedAddress(): string
    {
        return AddressValue::format($this->getAttributes());
    }
}
