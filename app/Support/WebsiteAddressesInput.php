<?php

namespace App\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator as ValidatorFactory;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;

final class WebsiteAddressesInput
{
    public const MAX_ADDRESSES = 50;

    public const MAX_LOCATION_TYPE = 10;

    public const MAX_TOTAL_CONTACTS = 100;

    public const MAX_INPUT_FIELDS = 900;

    public static function rules(): array
    {
        $rules = [
            'addresses' => ['present', 'array', 'list', 'max:'.self::MAX_ADDRESSES],
            'addresses.*' => ['array:id,label,address_line_1,address_line_2,city,state,postal_code,country,is_primary,is_active,contacts_present,contacts_ready,emails_count,phones_count,emails,phones'],
            'addresses.*.id' => ['nullable', 'integer', 'min:1', 'distinct'],
            'addresses.*.is_primary' => ['required', 'boolean'],
            'addresses.*.is_active' => ['required', 'boolean'],
            'addresses.*.contacts_present' => ['sometimes', 'accepted'],
            'addresses.*.contacts_ready' => ['sometimes', 'accepted'],
            'addresses.*.emails_count' => ['sometimes', 'integer', 'min:0'],
            'addresses.*.phones_count' => ['sometimes', 'integer', 'min:0'],
            'addresses.*.emails' => ['sometimes', 'array', 'list', 'max:'.self::MAX_LOCATION_TYPE],
            'addresses.*.phones' => ['sometimes', 'array', 'list', 'max:'.self::MAX_LOCATION_TYPE],
            'removed_addresses' => ['sometimes', 'array', 'list', 'max:'.self::MAX_ADDRESSES],
            'removed_addresses.*' => ['array:id,contact_ids'],
            'removed_addresses.*.id' => ['required', 'integer', 'min:1', 'distinct'],
            'removed_addresses.*.contact_ids' => ['present', 'array', 'list', 'max:'.self::MAX_TOTAL_CONTACTS],
            'removed_addresses.*.contact_ids.*' => ['integer', 'min:1', 'distinct'],
        ];
        foreach (['label' => 120, 'address_line_1' => 2000, 'address_line_2' => 255, 'city' => 120, 'state' => 120, 'postal_code' => 32, 'country' => 120] as $field => $max) {
            $rules['addresses.*.'.$field] = [
                in_array($field, ['label', 'address_line_1'], true) ? 'required' : 'nullable',
                'string', 'max:'.$max,
                $field === 'address_line_1' ? 'not_regex:/[\x00-\x09\x0b\x0c\x0e-\x1f\x7f-\x9f]/u' : 'not_regex:/[\x00-\x1f\x7f-\x9f]/u',
            ];
        }

        return $rules;
    }

    public static function after(Validator $validator): void
    {
        $seen = [];
        $primaries = 0;
        $total = 0;
        $rows = $validator->getData()['addresses'] ?? [];
        foreach (is_array($rows) ? $rows : [] as $index => $row) {
            if (! is_array($row)) {
                continue;
            }
            $prefix = 'addresses.'.$index;
            if (! $validator->errors()->has($prefix.'.label') && ! $validator->errors()->has($prefix.'.address_line_1')) {
                try {
                    $hash = AddressValue::fingerprint($row);
                    if (isset($seen[$hash])) {
                        $validator->errors()->add($prefix.'.label', 'This duplicates the same labeled address. Use a distinct label for an intentional shared location.');
                    }
                    $seen[$hash] = true;
                } catch (\Throwable) {
                    $validator->errors()->add($prefix.'.label', 'Address fields must contain valid text.');
                }
            }
            if (in_array($row['is_primary'] ?? false, [true, 1, '1'], true)) {
                $primaries++;
                if (! in_array($row['is_active'] ?? false, [true, 1, '1'], true)) {
                    $validator->errors()->add($prefix.'.is_primary', 'A primary address must be active.');
                }
            }
            if (isset($row['contacts_present'])) {
                try {
                    WebsiteContactsInput::validate(Arr::only($row, ['emails', 'phones']), self::MAX_LOCATION_TYPE);
                } catch (ValidationException $exception) {
                    foreach ($exception->errors() as $key => $messages) {
                        foreach ($messages as $message) {
                            $validator->errors()->add($prefix.'.'.$key, $message);
                        }
                    }
                }
            } elseif (array_intersect(['emails', 'phones', 'contacts_ready', 'emails_count', 'phones_count'], array_keys($row)) !== []) {
                $validator->errors()->add($prefix.'.contacts_present', 'Submit a complete contact collection or omit it to preserve existing contacts.');
            }
            foreach (['emails', 'phones'] as $group) {
                $total += is_array($row[$group] ?? null) ? count($row[$group]) : 0;
            }
        }
        if ($primaries > 1) {
            $validator->errors()->add('addresses', 'Choose no more than one active primary business address.');
        }
        if ($total > self::MAX_TOTAL_CONTACTS) {
            $validator->errors()->add('addresses', 'The combined contact limit is 100 entries across all scopes.');
        }
    }

    public static function validate(array $data): array
    {
        $validator = ValidatorFactory::make($data, self::rules());
        $validator->after(self::after(...));

        return $validator->validate();
    }
}
