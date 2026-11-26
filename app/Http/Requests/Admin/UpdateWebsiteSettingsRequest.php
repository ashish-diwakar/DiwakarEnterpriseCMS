<?php

namespace App\Http\Requests\Admin;

use App\Support\Rbac;
use App\Support\SettingsSubmission;
use App\Support\WebsiteAddressesInput;
use App\Support\WebsiteContactsInput;
use Illuminate\Foundation\Http\FormRequest;

class UpdateWebsiteSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(Rbac::PERMISSION_SETTINGS_MANAGE) ?? false;
    }

    protected function prepareForValidation(): void
    {
        $data = $this->all();
        if ($this->boolean('contacts_present')) {
            $data += ['emails' => [], 'phones' => []];
        }
        if ($this->boolean('addresses_present')) {
            $data += ['addresses' => [], 'removed_addresses' => []];
            if (is_array($data['addresses'])) {
                foreach ($data['addresses'] as &$row) {
                    if (is_array($row) && isset($row['contacts_present'])) {
                        $row += ['emails' => [], 'phones' => []];
                    }
                }
                unset($row);
            }
            if (is_array($data['removed_addresses'])) {
                foreach ($data['removed_addresses'] as &$row) {
                    if (is_array($row)) {
                        $row += ['contact_ids' => []];
                    }
                }
                unset($row);
            }
        }
        $this->merge($data);
    }

    public function rules(): array
    {
        $rules = [
            'site_name' => ['required', 'string', 'max:120'],
            'tagline' => ['nullable', 'string', 'max:180'],
            'public_email' => ['prohibited'], 'primary_phone' => ['prohibited'],
            'secondary_phone' => ['prohibited'], 'address' => ['prohibited'],
            'facebook_url' => ['nullable', 'url:http,https', 'max:2048'],
            'instagram_url' => ['nullable', 'url:http,https', 'max:2048'],
            'linkedin_url' => ['nullable', 'url:http,https', 'max:2048'],
            'youtube_url' => ['nullable', 'url:http,https', 'max:2048'],
            'x_twitter_url' => ['nullable', 'url:http,https', 'max:2048'],
            'footer_text' => ['nullable', 'string', 'max:500'],
        ];
        $contacts = $this->hasAny(['contacts_present', 'contacts_ready', 'emails_count', 'phones_count', 'emails', 'phones']);
        $addresses = $this->hasAny(['addresses_present', 'addresses_ready', 'addresses', 'removed_addresses', 'addresses_count', 'removed_addresses_count']);
        if ($contacts) {
            $rules += WebsiteContactsInput::rules();
            $rules['contacts_present'] = ['required', 'accepted'];
            $rules['contacts_ready'] = ['required', 'accepted'];
            $rules['emails_count'] = ['required', 'integer', 'min:0'];
            $rules['phones_count'] = ['required', 'integer', 'min:0'];
        }
        if ($addresses) {
            $rules += WebsiteAddressesInput::rules();
            $rules['addresses_present'] = ['required', 'accepted'];
            $rules['addresses_ready'] = ['required', 'accepted'];
            $rules['addresses_count'] = ['required', 'integer', 'min:0'];
            $rules['removed_addresses_count'] = ['required', 'integer', 'min:0'];
        }
        $rules['collections_expected'] = ['sometimes', 'accepted'];
        if ($contacts || $addresses || $this->hasAny(['contacts_version', 'collections_complete', 'collections_expected'])) {
            $rules['contacts_version'] = ['required', 'integer', 'min:0'];
            $rules['collections_complete'] = ['required', 'accepted'];
        }

        return $rules;
    }

    public function after(): array
    {
        return [WebsiteContactsInput::after(...), WebsiteAddressesInput::after(...), SettingsSubmission::after(...)];
    }
}
