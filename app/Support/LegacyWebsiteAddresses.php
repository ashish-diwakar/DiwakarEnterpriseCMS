<?php

namespace App\Support;

use App\Models\WebsiteSetting;
use Illuminate\Validation\ValidationException;

final class LegacyWebsiteAddresses
{
    public static function hasValue(WebsiteSetting $owner): bool
    {
        return trim((string) ($owner->getAttributes()['address'] ?? ''), " \r\n") !== '';
    }

    public static function inspect(WebsiteSetting $owner): array
    {
        $raw = (string) ($owner->getAttributes()['address'] ?? '');
        $result = ['addresses' => [], 'issues' => []];
        if (trim($raw, " \r\n") === '') {
            return $result;
        }
        if (preg_match('/[\x00-\x09\x0b\x0c\x0e-\x1f\x7f-\x9f]/u', $raw)) {
            $result['issues'][] = 'Legacy address contains unsupported controls; reconcile the unchanged original.';

            return $result;
        }
        // Only explicit header-only "Label:" lines delimit blocks. Inline labels
        // and unlabeled paragraphs require human reconciliation; punctuation is not a delimiter.
        preg_match_all('/^([^\r\n:]{1,120}):[ ]*(?:\r\n|\n|$)/mu', $raw, $matches, PREG_OFFSET_CAPTURE);
        $headers = $matches[0];
        if ($headers === []) {
            if (preg_match('/^[^\r\n:]+:[ ]*\S/mu', $raw) || preg_match('/\R[ ]*\R/u', $raw)) {
                $result['issues'][] = 'Legacy address blocks require explicit reconciliation; the complete original was retained.';

                return $result;
            }
            $result['addresses'][] = self::row('Business address', $raw);
        } else {
            if (trim(substr($raw, 0, $headers[0][1])) !== '') {
                $result['issues'][] = 'Legacy address has an unlabeled preamble; reconcile before initialization.';

                return $result;
            }
            foreach ($headers as $index => [$header, $offset]) {
                $start = $offset + strlen($header);
                $end = $headers[$index + 1][1] ?? strlen($raw);
                $body = trim(substr($raw, $start, $end - $start), "\r\n");
                $label = trim($matches[1][$index][0], ' ');
                $result['addresses'][] = self::row($label, $body);
            }
        }
        try {
            WebsiteAddressesInput::validate(['addresses' => $result['addresses']]);
        } catch (ValidationException) {
            $result['issues'][] = 'Legacy address text cannot be converted safely within the approved limits; reconcile the preserved original.';
        }

        return $result;
    }

    private static function row(string $label, string $text): array
    {
        return ['label' => $label, 'address_line_1' => $text, 'is_active' => true,
            'is_primary' => preg_match('/^primary(?: business)? address$/iu', $label) === 1,
            'contacts_present' => 1, 'emails' => [], 'phones' => []];
    }
}
