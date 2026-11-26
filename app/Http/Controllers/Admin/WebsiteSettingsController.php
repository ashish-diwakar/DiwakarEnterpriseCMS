<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Settings\UpdateWebsiteSettings;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateWebsiteSettingsRequest;
use App\Models\WebsiteSetting;
use App\Support\AddressValue;
use App\Support\LegacyWebsiteAddresses;
use App\Support\LegacyWebsiteContacts;
use App\Support\SettingsSubmission;
use App\Support\WebsiteAddressesInput;
use App\Support\WebsiteContactsInput;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class WebsiteSettingsController extends Controller
{
    public function edit(): View
    {
        $settings = WebsiteSetting::singleton();
        $ready = Schema::hasColumns('website_contacts', ['website_address_id', 'scope_key'])
            && Schema::hasTable('website_addresses') && Schema::hasColumns('website_settings', ['contacts_initialized_at', 'addresses_initialized_at', 'contacts_version']);
        $editable = $ready && ($settings->contacts_initialized_at !== null || (! LegacyWebsiteContacts::hasValues($settings) && (! $settings->exists || ! $settings->contacts()->exists())));
        $addressesEditable = $ready && ($settings->addresses_initialized_at !== null || (! LegacyWebsiteAddresses::hasValue($settings) && (! $settings->exists || ! $settings->addresses()->exists())));
        $rows = ['emails' => [], 'phones' => []];
        if ($ready && $settings->contacts_initialized_at !== null) {
            $rows = $this->contactRows($settings->contacts);
        }
        $addresses = [];
        $addressContactIds = [];
        if ($ready && $settings->addresses_initialized_at !== null) {
            $settings->loadMissing('addresses.contacts');
            foreach ($settings->addresses as $address) {
                $addresses[] = [...$address->only(['id', ...AddressValue::FIELDS, 'is_primary', 'is_active']), ...$this->contactRows($address->contacts)];
                $addressContactIds[$address->id] = $address->contacts->pluck('id')->all();
            }
        }

        return view('admin.settings.edit', [
            'settings' => $settings, 'contactsEditable' => $editable, 'contactsSchemaReady' => $ready, 'addressesEditable' => $addressesEditable,
            'contactEditor' => [
                'emails' => old('emails', $rows['emails']), 'phones' => old('phones', $rows['phones']),
                'addresses' => old('addresses', $addresses), 'removedAddresses' => old('removed_addresses', []),
                'addressContactIds' => $addressContactIds, 'fieldLimit' => SettingsSubmission::fieldLimit(),
                'errors' => session('errors')?->getBag('default')->getMessages() ?? [], 'max' => WebsiteContactsInput::MAX_PER_TYPE,
                'addressMax' => WebsiteAddressesInput::MAX_ADDRESSES, 'locationMax' => WebsiteAddressesInput::MAX_LOCATION_TYPE,
            ],
        ]);
    }

    private function contactRows(iterable $contacts): array
    {
        $rows = ['emails' => [], 'phones' => []];
        foreach ($contacts as $contact) {
            $rows[$contact->type === 'email' ? 'emails' : 'phones'][] = $contact->only(['id', 'label', 'value', 'subtype', 'is_primary', 'is_active']);
        }

        return $rows;
    }

    public function update(UpdateWebsiteSettingsRequest $request, UpdateWebsiteSettings $action): RedirectResponse
    {
        $action->handle($request->validated());

        return redirect()->route('admin.settings.edit')->with('status', 'Website settings updated.');
    }
}
