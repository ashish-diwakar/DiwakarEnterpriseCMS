<?php

namespace App\Support;

use Illuminate\Validation\Validator;

final class SettingsSubmission
{
    public static function fieldLimit(): int
    {
        $phpLimit = (int) ini_get('max_input_vars');

        return $phpLimit > 0 ? max(1, min(WebsiteAddressesInput::MAX_INPUT_FIELDS, $phpLimit - 20)) : WebsiteAddressesInput::MAX_INPUT_FIELDS;
    }

    public static function after(Validator $validator): void
    {
        $data = $validator->getData();
        $collection = isset($data['contacts_present']) || isset($data['addresses_present']) || isset($data['collections_expected']);
        if (! $collection) {
            return;
        }
        if (isset($data['collections_expected']) && ! isset($data['contacts_present']) && ! isset($data['addresses_present'])) {
            $validator->errors()->add('collections_complete', 'Collection fields are missing. Reload the editor; no changes were saved.');
        }
        foreach (['emails', 'phones', 'addresses', 'removed_addresses'] as $group) {
            if (in_array($group, ['emails', 'phones'], true) ? isset($data['contacts_present']) : isset($data['addresses_present'])) {
                self::count($validator, $data, $group, '');
            }
        }
        foreach (is_array($data['addresses'] ?? null) ? $data['addresses'] : [] as $index => $row) {
            if (! is_array($row) || ! isset($row['contacts_present'])) {
                continue;
            }
            $prefix = 'addresses.'.$index.'.';
            if (! in_array($row['contacts_ready'] ?? null, [true, 1, '1'], true)) {
                $validator->errors()->add($prefix.'contacts_ready', 'The address contact editor must load completely before saving.');
            }
            self::count($validator, $row, 'emails', $prefix);
            self::count($validator, $row, 'phones', $prefix);
        }
        $leaves = 0;
        array_walk_recursive($data, function () use (&$leaves): void {
            $leaves++;
        });
        if ($leaves > self::fieldLimit()) {
            $validator->errors()->add('collections_complete', 'This submission exceeds the safe field budget. Save a smaller collection of contacts and addresses.');
        }
    }

    private static function count(Validator $validator, array $data, string $group, string $prefix): void
    {
        $declared = $data[$group.'_count'] ?? null;
        if (filter_var($declared, FILTER_VALIDATE_INT) === false || ! is_array($data[$group] ?? null) || (int) $declared !== count($data[$group])) {
            $validator->errors()->add($prefix.$group.'_count', 'The submitted collection is incomplete. Reload the editor; no collection changes were saved.');
        }
    }
}
