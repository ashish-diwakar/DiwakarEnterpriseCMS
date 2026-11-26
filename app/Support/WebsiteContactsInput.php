<?php

namespace App\Support;

use Illuminate\Support\Facades\Validator as ValidatorFactory;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

final class WebsiteContactsInput
{
    public const MAX_PER_TYPE = 50;

    public static function rules(int $max = self::MAX_PER_TYPE): array
    {
        $rules = [];
        foreach (['emails', 'phones'] as $group) {
            $fields = $group === 'emails' ? 'id,label,value,is_primary,is_active' : 'id,label,value,is_primary,is_active,subtype';
            $rules[$group] = ['present', 'array', 'list', 'max:'.$max];
            $rules[$group.'.*'] = ['array:'.$fields];
            $rules[$group.'.*.id'] = ['nullable', 'integer', 'min:1'];
            $rules[$group.'.*.label'] = ['nullable', 'string', 'max:120', 'not_regex:/[\x00-\x1f\x7f]/'];
            $rules[$group.'.*.value'] = ['required', 'string', 'max:'.($group === 'emails' ? 255 : 80)];
            $rules[$group.'.*.is_primary'] = ['required', 'boolean'];
            $rules[$group.'.*.is_active'] = ['required', 'boolean'];
        }
        $rules['phones.*.subtype'] = ['required', Rule::in(array_keys(ContactValue::PHONE_TYPES))];

        return $rules;
    }

    public static function after(Validator $validator): void
    {
        $ids = [];
        foreach (['emails', 'phones'] as $group) {
            $rows = $validator->getData()[$group] ?? [];
            if (! is_array($rows)) {
                continue;
            }
            $seen = [];
            $primaries = 0;
            foreach ($rows as $index => $row) {
                if (! is_array($row)) {
                    continue;
                }
                $prefix = $group.'.'.$index;
                if (isset($row['id']) && is_scalar($row['id']) && filter_var($row['id'], FILTER_VALIDATE_INT) !== false) {
                    $id = (int) $row['id'];
                    if (isset($ids[$id])) {
                        $validator->errors()->add($prefix.'.id', 'A contact ID may appear only once.');
                    }
                    $ids[$id] = true;
                }
                if (is_string($row['value'] ?? null) && ($group === 'emails' || is_string($row['subtype'] ?? null))) {
                    $normalized = $group === 'emails' ? ContactValue::email($row['value']) : ContactValue::phone($row['value'], $row['subtype']);
                    if ($normalized === null) {
                        $validator->errors()->add($prefix.'.value', $group === 'emails' ? 'Enter one valid email address without controls or a list of addresses.' : 'Enter one phone number (7–15 digits), optional leading +, and a validated landline/Other extension (x or ext, 1–8 digits).');
                    } elseif (isset($seen[$normalized])) {
                        $validator->errors()->add($prefix.'.value', 'This contact duplicates another entry after normalization.');
                    } else {
                        $seen[$normalized] = true;
                    }
                }
                if (in_array($row['is_primary'] ?? false, [true, 1, '1'], true)) {
                    $primaries++;
                    if (! in_array($row['is_active'] ?? false, [true, 1, '1'], true)) {
                        $validator->errors()->add($prefix.'.is_primary', 'A primary contact must be active. Choose another primary or clear this designation.');
                    }
                }
            }
            if ($primaries > 1) {
                $validator->errors()->add($group, 'Choose no more than one primary contact of this type.');
            }
        }
    }

    public static function validate(array $data, int $max = self::MAX_PER_TYPE): array
    {
        $validator = ValidatorFactory::make($data, self::rules($max));
        $validator->after(self::after(...));

        return $validator->validate();
    }
}
