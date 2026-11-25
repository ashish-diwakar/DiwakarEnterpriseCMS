<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SafeMenuUrl implements ValidationRule
{
    public static function accepts(mixed $value): bool
    {
        if (! is_string($value) || $value === '' || strlen($value) > 2048 || preg_match('/[\x00-\x20\x7f\\\\]/', $value) || preg_match('/%(?![0-9a-f]{2})/i', $value)) {
            return false;
        }
        $decoded = $value;
        for ($i = 0; $i < 5; $i++) {
            $next = rawurldecode($decoded);
            if ($next === $decoded) {
                break;
            }
            $decoded = $next;
        }
        if ($next !== rawurldecode($decoded) || preg_match('/[\x00-\x1f\x7f\\\\]/', $decoded)) {
            return false;
        }
        foreach ([$value, $decoded] as $url) {
            if (str_starts_with($url, '/')) {
                if (str_starts_with($url, '//')) {
                    return false;
                }

                continue;
            }
            if (! preg_match('~\Ahttps?://~i', $url) || ($url === $value && ! filter_var($url, FILTER_VALIDATE_URL))) {
                return false;
            }
            $parts = parse_url($url);
            if ($parts === false || empty($parts['host']) || isset($parts['user']) || isset($parts['pass'])) {
                return false;
            }
        }

        return true;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! self::accepts($value)) {
            $fail('Use a site-relative URL beginning with one slash, or an HTTP/HTTPS URL without credentials, controls or backslashes (maximum 2048 characters).');
        }
    }
}
