<?php

namespace App\Libraries;

/**
 * Encrypts/decrypts uploaded document contents at rest, using the app's
 * configured encryption.key (see Config\Encryption).
 */
class EncryptedFileStorage
{
    /**
     * Encrypts the bytes at $sourcePath and writes the ciphertext to $destPath.
     */
    public static function storeFromPath(string $sourcePath, string $destPath): void
    {
        $plain = file_get_contents($sourcePath);

        file_put_contents($destPath, service('encrypter')->encrypt($plain));
    }

    /**
     * Reads the ciphertext at $path and returns the decrypted bytes.
     */
    public static function read(string $path): string
    {
        return service('encrypter')->decrypt(file_get_contents($path));
    }
}
