<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

/**
 * Encrypts an identity number at rest, and tolerates plaintext on the way in.
 *
 * Aadhaar and PAN have to be collected here, so the question is how they are
 * stored rather than whether. $hidden already keeps them out of JSON, but that
 * does nothing if the database itself is read - a stolen backup, an injection,
 * a shared database host. This puts ciphertext in the column instead.
 *
 * Laravel's built-in `encrypted` cast throws on any value that is not valid
 * ciphertext, which would mean every existing row breaking the instant the cast
 * went live. This one reads a plaintext value back unchanged and always writes
 * ciphertext, so the column can be migrated gradually and a half-converted
 * table keeps working. Run `identity:encrypt-existing` to convert the rest.
 *
 * It protects against the database being read, not against the whole server
 * being taken: APP_KEY lives in .env on the same machine.
 */
class EncryptedIdentity implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null || $value === '') {
            return $value;
        }

        try {
            return Crypt::decryptString($value);
        } catch (DecryptException) {
            // Not encrypted yet. A 12-digit Aadhaar or a 10-character PAN can
            // never be mistaken for Laravel's ciphertext envelope, so this is
            // unambiguous rather than a guess.
            return $value;
        }
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): array
    {
        if ($value === null || $value === '') {
            return [$key => $value];
        }

        return [$key => Crypt::encryptString((string) $value)];
    }

    /**
     * Whether a stored value has already been converted. Used by the backfill
     * so it can be run repeatedly without double-encrypting anything.
     */
    public static function isEncrypted(?string $value): bool
    {
        if ($value === null || $value === '') {
            return false;
        }

        try {
            Crypt::decryptString($value);

            return true;
        } catch (DecryptException) {
            return false;
        }
    }
}
