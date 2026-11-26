<?php

namespace App\Actions\Settings;

use App\Models\WebsiteAddress;
use App\Models\WebsiteContact;
use App\Models\WebsiteSetting;
use App\Support\AddressValue;
use App\Support\WebsiteAddressesInput;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use LogicException;

class SyncWebsiteAddresses
{
    public function __construct(private readonly SyncWebsiteContacts $contacts) {}

    /** The caller owns the singleton lock and the encompassing transaction. */
    public function handle(WebsiteSetting $owner, array $data, bool $touchVersion = true): void
    {
        if ($owner->id !== WebsiteSetting::SINGLETON_ID || $owner->getConnection()->transactionLevel() === 0) {
            throw new LogicException('Address synchronization requires the locked singleton transaction.');
        }
        $data = WebsiteAddressesInput::validate($data);
        $existing = $owner->addresses()->lockForUpdate()->get()->keyBy('id');
        $retained = [];
        foreach ($data['addresses'] as $index => $row) {
            if (isset($row['id'])) {
                if (! $existing->has((int) $row['id'])) {
                    throw ValidationException::withMessages(['addresses.'.$index.'.id' => 'This address does not belong to the current settings.']);
                }
                $retained[] = (int) $row['id'];
            }
        }
        $removals = collect($data['removed_addresses'] ?? [])->keyBy(fn ($row) => (int) $row['id']);
        foreach ($removals as $id => $removal) {
            if (! $existing->has($id) || in_array($id, $retained, true)) {
                throw ValidationException::withMessages(['removed_addresses' => 'A removal must identify an omitted address belonging to these settings.']);
            }
        }
        foreach ($existing->except($retained) as $address) {
            $ids = $address->contacts()->lockForUpdate()->pluck('id')->sort()->values()->all();
            $acknowledged = array_map('intval', $removals->get($address->id)['contact_ids'] ?? []);
            sort($acknowledged);
            if ($ids !== [] && $acknowledged !== $ids) {
                throw ValidationException::withMessages(['addresses' => 'Remove every contact explicitly before removing its address. Existing address contacts were preserved.']);
            }
            if ($acknowledged !== $ids) {
                throw ValidationException::withMessages(['removed_addresses' => 'The acknowledged contacts do not match the address. Reload before removing it.']);
            }
        }
        // No cascade: the exact, explicitly acknowledged children are removed first.
        foreach ($existing->except($retained) as $address) {
            $address->contacts()->delete();
            $address->deleteOrFail();
        }
        $owner->addresses()->update(['is_primary' => false]);
        foreach ($retained as $id) {
            $existing[$id]->forceFill(['is_primary' => false, 'normalized_hash' => hash('sha256', Str::uuid().':'.$id)])->saveOrFail();
        }
        foreach ($data['addresses'] as $index => $row) {
            $address = isset($row['id']) ? $existing[(int) $row['id']] : new WebsiteAddress;
            $address->fill(Arr::only($row, [...AddressValue::FIELDS, 'is_primary', 'is_active']))->forceFill([
                'website_setting_id' => $owner->id, 'sort_order' => $index + 1,
                'normalized_hash' => AddressValue::fingerprint($row),
            ])->saveOrFail();
            if (isset($row['contacts_present'])) {
                try {
                    $this->contacts->handle($owner, Arr::only($row, ['emails', 'phones']), $address, false);
                } catch (ValidationException $exception) {
                    $errors = [];
                    foreach ($exception->errors() as $key => $messages) {
                        $errors['addresses.'.$index.'.'.$key] = $messages;
                    }
                    throw ValidationException::withMessages($errors);
                }
            }
        }
        $owner->forceFill(['addresses_initialized_at' => $owner->addresses_initialized_at ?? now()]);
        if ($touchVersion) {
            if (WebsiteContact::where('website_setting_id', $owner->id)->count() > WebsiteAddressesInput::MAX_TOTAL_CONTACTS) {
                throw ValidationException::withMessages(['addresses' => 'The combined contact limit is 100 entries across all scopes.']);
            }
            $owner->forceFill(['contacts_version' => ($owner->contacts_version ?? 0) + 1]);
        }
        $owner->saveOrFail();
        $owner->unsetRelation('addresses');
    }
}
