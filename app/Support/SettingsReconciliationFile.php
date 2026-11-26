<?php

namespace App\Support;

use InvalidArgumentException;
use JsonException;

final class SettingsReconciliationFile
{
    public static function read(?string $file): ?array
    {
        if ($file === null || $file === '') {
            return null;
        }
        $path = realpath($file);
        $public = strtolower(str_replace('\\', '/', realpath(public_path()))).'/';
        if (! $path || ! is_file($path) || ! is_readable($path) || filesize($path) > 262144 || str_starts_with(strtolower(str_replace('\\', '/', $path)), $public)) {
            throw new InvalidArgumentException('Use a readable protected JSON file outside the public directory, no larger than 256 KiB.');
        }
        try {
            $data = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            throw new InvalidArgumentException('The reconciliation file is not valid JSON.');
        }
        if (! is_array($data)) {
            throw new InvalidArgumentException('Reconciliation must contain a JSON object.');
        }

        return $data;
    }
}
