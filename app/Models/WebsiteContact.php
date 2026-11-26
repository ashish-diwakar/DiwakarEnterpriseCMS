<?php

namespace App\Models;

use App\Support\ContactValue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebsiteContact extends Model
{
    protected $fillable = ['label', 'value', 'subtype', 'is_primary', 'is_active'];

    protected $attributes = ['is_primary' => false, 'is_active' => true];

    protected function casts(): array
    {
        return ['is_primary' => 'boolean', 'is_active' => 'boolean', 'sort_order' => 'integer'];
    }

    public function websiteSetting(): BelongsTo
    {
        return $this->belongsTo(WebsiteSetting::class);
    }

    public function websiteAddress(): BelongsTo
    {
        return $this->belongsTo(WebsiteAddress::class);
    }

    private function visibleScope(): bool
    {
        return $this->website_address_id === null || ($this->websiteAddress?->publiclyAvailable() ?? false);
    }

    public function mailtoUrl(): ?string
    {
        $email = $this->type === 'email' && $this->is_active && $this->visibleScope() ? ContactValue::email($this->value) : null;
        if ($email === null || $email !== $this->normalized_value) {
            return null;
        }
        [$local, $domain] = explode('@', $email, 2);

        return 'mailto:'.rawurlencode($local).'@'.rawurlencode($domain);
    }

    public function telUrl(): ?string
    {
        $number = $this->type === 'phone' && $this->is_active && $this->visibleScope() ? ContactValue::phone($this->value, $this->subtype ?? 'other') : null;

        return $number !== null && $number === $this->normalized_value ? 'tel:'.$number : null;
    }
}
