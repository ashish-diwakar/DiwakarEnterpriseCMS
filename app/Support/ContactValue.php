<?php

namespace App\Support;

final class ContactValue
{
    public const PHONE_TYPES = ['mobile' => 'Mobile', 'landline' => 'Landline', 'toll_free' => 'Toll-free', 'other' => 'Other'];

    public static function email(string $value): ?string
    {
        if (preg_match('/[\x00-\x1f\x7f-\xff,;<>" ]/', trim($value, ' '))) {
            return null;
        }
        $value = trim($value, ' ');
        if (strlen($value) > 255 || substr_count($value, '@') !== 1 || ! filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        return strtolower($value);
    }

    public static function phone(string $value, string $subtype = 'other'): ?string
    {
        if (strlen($value) > 80 || preg_match('/[\x00-\x1f\x7f-\xff]/', $value) || ! isset(self::PHONE_TYPES[$subtype])) {
            return null;
        }
        $value = trim($value, ' ');
        if (! preg_match('/\A(\+?[0-9(][0-9 .()\-]*?)(?:\s*(?:extension|ext\.?|x)\s*([0-9]{1,8}))?\z/i', $value, $matches)) {
            return null;
        }
        $number = trim($matches[1]);
        $extension = $matches[2] ?? null;
        if ($extension !== null && ! in_array($subtype, ['landline', 'other'], true)) {
            return null;
        }
        $balance = 0;
        foreach (str_split($number) as $character) {
            $balance += $character === '(' ? 1 : ($character === ')' ? -1 : 0);
            if ($balance < 0 || $balance > 1) {
                return null;
            }
        }
        $digits = preg_replace('/\D/', '', $number);
        if ($balance !== 0 || strlen($digits) < 7 || strlen($digits) > 15 || ! preg_match('/[1-9]/', $digits)) {
            return null;
        }
        if (str_starts_with($number, '+') && ! preg_match('/\A\+[1-9]/', $number)) {
            return null;
        }
        if (str_starts_with($number, '+') && str_contains($number, '(0)')) {
            return null;
        }

        return (str_starts_with($number, '+') ? '+' : '').$digits.($extension !== null ? ';ext='.$extension : '');
    }
}
