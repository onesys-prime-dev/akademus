<?php

namespace App\Casts;

use CodeIgniter\DataCaster\Cast\CastInterface;

/**
 * Stores an array as JSON, encrypted at rest. Wraps EncryptedCast, so it uses the
 * same key and storage format (base64 of the ciphertext) as the other PII columns.
 *
 * Column must be TEXT/LONGTEXT, not a native JSON column.
 */
class EncryptedJsonCast implements CastInterface
{
    public static function get(mixed $value, array $params = [], ?object $helper = null): mixed
    {
        if ($value === null || $value === '') {
            return [];
        }

        $decoded = json_decode((string) EncryptedCast::get($value, $params, $helper), true);

        return is_array($decoded) ? $decoded : [];
    }

    public static function set(mixed $value, array $params = [], ?object $helper = null): mixed
    {
        if ($value === null || $value === '' || $value === []) {
            return null;
        }

        return EncryptedCast::set(json_encode($value, JSON_UNESCAPED_UNICODE), $params, $helper);
    }
}
