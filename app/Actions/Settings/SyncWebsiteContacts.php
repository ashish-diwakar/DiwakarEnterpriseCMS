<?php

namespace App\Actions\Settings;

use App\Models\WebsiteAddress;
use App\Models\WebsiteContact;
use App\Models\WebsiteSetting;
use App\Support\ContactValue;
use App\Support\WebsiteAddressesInput;
use App\Support\WebsiteContactsInput;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use LogicException;

class SyncWebsiteContacts
{
    /** Caller must hold the singleton row lock inside its transaction. */
    public function handle(WebsiteSetting $owner, array $data, ?WebsiteAddress $address = null, bool $touchVersion = true): void
    {
        if ($owner->id !== WebsiteSetting::SINGLETON_ID || $owner->getConnection()->transactionLevel() === 0) {
            throw new LogicException('Contact synchronization requires the locked singleton transaction.');
        }
        if ($address !== null && (! $address->exists || $address->website_setting_id !== $owner->id)) {
            throw ValidationException::withMessages(['addresses' => 'This address does not belong to the current settings.']);
        }
        $data = WebsiteContactsInput::validate($data, $address ? WebsiteAddressesInput::MAX_LOCATION_TYPE : WebsiteContactsInput::MAX_PER_TYPE);
        $scope = fn () => $address ? $address->contacts() : $owner->contacts();
        $existing = $scope()->lockForUpdate()->get()->keyBy('id');
        $retained = [];
        foreach (['emails' => 'email', 'phones' => 'phone'] as $group => $type) {
            foreach ($data[$group] as $index => $row) {
                if (isset($row['id'])) {
                    $contact = $existing->get((int) $row['id']);
                    if (! $contact || $contact->type !== $type) {
                        throw ValidationException::withMessages([$group.'.'.$index.'.id' => 'This contact does not belong to the current settings and contact type.']);
                    }
                    $retained[] = $contact->id;
                }
            }
        }
        $scope()->update(['is_primary' => false]);
        $scope()->whereNotIn('id', $retained)->delete();
        // Permit valid value swaps without violating the unique canonical-value key.
        // Temporary keys exist only within this transaction and never change display values.
        $prefix = 'sync:'.Str::uuid().':';
        foreach ($retained as $id) {
            $existing[$id]->forceFill(['is_primary' => false, 'normalized_value' => $prefix.$id])->saveOrFail();
        }
        foreach (['emails' => 'email', 'phones' => 'phone'] as $group => $type) {
            foreach ($data[$group] as $index => $row) {
                $contact = isset($row['id']) ? $existing[(int) $row['id']] : new WebsiteContact;
                $contact->fill([
                    'label' => $row['label'] ?? null, 'value' => trim($row['value'], ' '),
                    'subtype' => $type === 'phone' ? $row['subtype'] : null,
                    'is_primary' => $row['is_primary'], 'is_active' => $row['is_active'],
                ])->forceFill([
                    'website_setting_id' => $owner->id, 'website_address_id' => $address?->id, 'type' => $type, 'sort_order' => $index + 1,
                    'normalized_value' => $type === 'email' ? ContactValue::email($row['value']) : ContactValue::phone($row['value'], $row['subtype']),
                ])->saveOrFail();
            }
        }
        if ($address === null) {
            $owner->forceFill(['contacts_initialized_at' => $owner->contacts_initialized_at ?? now()]);
        }
        if ($touchVersion) {
            if (WebsiteContact::where('website_setting_id', $owner->id)->count() > WebsiteAddressesInput::MAX_TOTAL_CONTACTS) {
                throw ValidationException::withMessages(['contacts' => 'The combined contact limit is 100 entries across all scopes.']);
            }
            $owner->forceFill(['contacts_version' => ($owner->contacts_version ?? 0) + 1]);
        }
        $owner->saveOrFail();
        $owner->unsetRelation('contacts');
        $address?->unsetRelation('contacts');
    }
}
