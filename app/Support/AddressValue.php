<?php

namespace App\Support;

final class AddressValue
{
    public const FIELDS = ['label', 'address_line_1', 'address_line_2', 'city', 'state', 'postal_code', 'country'];

    public static function fingerprint(array $row): string
    {
        $values = [];
        foreach (self::FIELDS as $field) {
            $values[] = mb_strtolower(preg_replace('/\s+/u', ' ', trim((string) ($row[$field] ?? ''))), 'UTF-8');
        }

        return hash('sha256', json_encode($values, JSON_THROW_ON_ERROR));
    }

    public static function format(array $row): string
    {
        $lines = [];
        foreach (array_slice(self::FIELDS, 1) as $field) {
            foreach (preg_split('/\R/u', (string) ($row[$field] ?? '')) as $line) {
                $line = trim($line);
                $key = mb_strtolower(preg_replace('/\s+/u', ' ', $line), 'UTF-8');
                if ($key !== '' && ! isset($lines[$key])) {
                    $lines[$key] = $line;
                }
            }
        }

        return implode("\n", $lines);
    }
}
