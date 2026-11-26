<?php

namespace App\Actions\Settings;

use App\Models\WebsiteContact;
use App\Models\WebsiteSetting;
use App\Support\LegacyWebsiteAddresses;
use App\Support\LegacyWebsiteContacts;
use App\Support\WebsiteAddressesInput;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class UpdateWebsiteSettings
{
    public function __construct(private readonly SyncWebsiteContacts $contacts, private readonly SyncWebsiteAddresses $addresses) {}

    public function handle(array $data): WebsiteSetting
    {
        try {
            return DB::transaction(function () use ($data): WebsiteSetting {
                $attributes = Arr::only($data, ['site_name', 'tagline', 'facebook_url', 'instagram_url', 'linkedin_url', 'youtube_url', 'x_twitter_url', 'footer_text']);
                $owner = WebsiteSetting::saveSingleton($attributes);
                $owner = WebsiteSetting::query()->whereKey($owner->id)->lockForUpdate()->firstOrFail();
                if (! isset($data['contacts_present']) && ! isset($data['addresses_present'])) {
                    return $owner;
                }
                if (! Schema::hasColumns('website_contacts', ['website_address_id', 'scope_key']) || ! Schema::hasTable('website_addresses')) {
                    throw ValidationException::withMessages(['contacts' => 'Install all three Settings collection migrations before editing collections.']);
                }
                if (($owner->contacts_version ?? 0) !== (int) $data['contacts_version']) {
                    throw ValidationException::withMessages(['contacts_version' => 'Settings collections changed in another request. Reload Settings before saving again.']);
                }
                if (isset($data['contacts_present'])) {
                    if ($owner->contacts_initialized_at === null && (LegacyWebsiteContacts::hasValues($owner) || $owner->contacts()->exists())) {
                        throw ValidationException::withMessages(['contacts' => 'Initialize and verify preserved default contacts manually before replacing this collection.']);
                    }
                    $this->contacts->handle($owner, Arr::only($data, ['emails', 'phones']), touchVersion: false);
                }
                if (isset($data['addresses_present'])) {
                    if ($owner->addresses_initialized_at === null && (LegacyWebsiteAddresses::hasValue($owner) || $owner->addresses()->exists())) {
                        throw ValidationException::withMessages(['addresses' => 'Initialize and verify the preserved legacy address manually before replacing this collection.']);
                    }
                    $this->addresses->handle($owner, Arr::only($data, ['addresses', 'removed_addresses']), false);
                }
                if (WebsiteContact::where('website_setting_id', $owner->id)->count() > WebsiteAddressesInput::MAX_TOTAL_CONTACTS) {
                    throw ValidationException::withMessages(['contacts' => 'The combined limit is 100 contact entries across all scopes.']);
                }
                $owner->forceFill(['contacts_version' => ($owner->contacts_version ?? 0) + 1])->saveOrFail();

                return $owner;
            });
        } catch (UniqueConstraintViolationException) {
            throw ValidationException::withMessages(['contacts' => 'A labeled address, contact value or primary designation conflicts within its scope. Reload and review the collections.']);
        }
    }
}
