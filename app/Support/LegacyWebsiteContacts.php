<?php

namespace App\Support;

use App\Models\WebsiteSetting;

final class LegacyWebsiteContacts
{
    public static function hasValues(WebsiteSetting $settings): bool
    {
        foreach (['public_email', 'primary_phone', 'secondary_phone'] as $field) {
            if (trim((string) ($settings->getAttributes()[$field] ?? ''), ' ') !== '') {
                return true;
            }
        }

        return false;
    }

    public static function inspect(WebsiteSetting $settings): array
    {
        $result = ['emails' => [], 'phones' => [], 'issues' => [], 'duplicates' => 0];
        $seen = [];
        foreach (['public_email', 'primary_phone', 'secondary_phone'] as $field) {
            $raw = (string) ($settings->getAttributes()[$field] ?? '');
            if (trim($raw, ' ') === '') {
                continue;
            }
            $email = $field === 'public_email';
            $group = $email ? 'emails' : 'phones';
            $identified = 0;
            $parts = preg_split($email ? '/[,;\r\n]+/' : '/[,|\r\n]+/', $raw);
            foreach ($parts as $index => $value) {
                if (trim($value, ' ') === '') {
                    continue;
                }
                $normalized = $email ? ContactValue::email($value) : ContactValue::phone($value);
                if ($normalized === null) {
                    $result['issues'][] = $field.' entry '.($index + 1).' requires reconciliation; original data retained.';

                    continue;
                }
                $identified++;
                if (isset($seen[$group][$normalized])) {
                    $result['duplicates']++;

                    continue;
                }
                $seen[$group][$normalized] = true;
                $row = ['label' => $email ? 'Public email' : ($field === 'primary_phone' ? 'Primary phone' : 'Secondary phone'),
                    'value' => trim($value, ' '), 'is_active' => true,
                    'is_primary' => $identified === 1 && $field !== 'secondary_phone'];
                if (! $email) {
                    $row['subtype'] = 'other';
                }
                $result[$group][] = $row;
            }
            if ($identified === 0 && $result['issues'] === []) {
                $result['issues'][] = $field.' contains no identifiable contact; original data retained.';
            }
        }
        foreach (['emails', 'phones'] as $group) {
            if (count($result[$group]) > WebsiteContactsInput::MAX_PER_TYPE) {
                $result['issues'][] = $group.' exceeds the submission limit; reconcile before initialization.';
            }
        }

        return $result;
    }
}
