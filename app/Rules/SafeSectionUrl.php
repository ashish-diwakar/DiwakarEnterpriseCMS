<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SafeSectionUrl implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('Use a root-relative path or a valid HTTP/HTTPS URL.');

            return;
        }
        $decoded = $value;
        // Inspect nested encoding too; never turn an encoded scheme into an accepted URL.
        for ($i = 0; $i < strlen($value); $i++) {
            $next = rawurldecode($decoded);
            if (preg_match('/[\x00-\x1f\x7f\\\\]/', $decoded) || preg_match('/\s/', $value) || str_starts_with($decoded, '//')) {
                $fail('The destination contains unsafe URL syntax.');

                return;
            }
            if ($next === $decoded) {
                break;
            }
            $decoded = $next;
        }
        $parts = parse_url($value);
        $internal = str_starts_with($value, '/') && ! str_starts_with($value, '//')
            && is_array($parts) && ! isset($parts['scheme']) && ! isset($parts['host']);
        $external = filter_var($value, FILTER_VALIDATE_URL) && is_array($parts)
            && in_array(strtolower($parts['scheme'] ?? ''), ['http', 'https'], true)
            && ! empty($parts['host']) && ! isset($parts['user']) && ! isset($parts['pass']);
        if (! $internal && ! $external) {
            $fail('Use a root-relative path or a valid HTTP/HTTPS URL without credentials.');
        }
    }
}
