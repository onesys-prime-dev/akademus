<?php

namespace App\Casts;

use CodeIgniter\DataCaster\Cast\CastInterface;

/**
 * Transparently encrypts a scalar field on write and decrypts it on read,
 * using the app's configured encryption.key (see Config\Encryption).
 *
 * Column must be able to hold ciphertext (TEXT), not the original type,
 * since AES-256-CTR + HMAC output is far longer than the plaintext.
 */
class EncryptedCast implements CastInterface
{
    public static function get(mixed $value, array $params = [], ?object $helper = null): mixed
    {
        if ($value === null || $value === '') {
            return $value;
        }

        return service('encrypter')->decrypt(base64_decode((string) $value, true));
    }

    public static function set(mixed $value, array $params = [], ?object $helper = null): mixed
    {
        if ($value === null || $value === '') {
            return $value;
        }

        return base64_encode(service('encrypter')->encrypt((string) $value));
    }
}
