<?php

declare(strict_types=1);

namespace App\Libraries;

use InvalidArgumentException;
use RuntimeException;

/**
 * Two-way PII encryption (Sodium secretbox) and HMAC lookup hashing.
 *
 * Key material is read from the environment (`pii.key`) unless injected
 * explicitly (e.g. in tests).
 */
class PiiCipher
{
    private const KEY_BYTES = 32;

    private const NONCE_BYTES = 24;

    private const MAC_BYTES = 16;

    private const LOOKUP_KEY_CONTEXT = 'pii-lookup';

    private readonly string $key;

    private readonly string $lookupKey;

    public function __construct(?string $keyMaterial = null)
    {
        if (! extension_loaded('sodium')) {
            throw new RuntimeException('The sodium extension is required for PiiCipher.');
        }

        $keyMaterial ??= (string) env('pii.key');

        if ($keyMaterial === '') {
            throw new RuntimeException('pii.key is not configured in the environment.');
        }

        $this->key       = $this->decodeKey($keyMaterial);
        $this->lookupKey = sodium_crypto_generichash(
            self::LOOKUP_KEY_CONTEXT . $this->key,
            '',
            self::KEY_BYTES,
        );
    }

    /**
     * Encrypt plaintext for storage in a *_cipher column.
     */
    public function encrypt(string $plaintext): string
    {
        $nonce  = random_bytes(self::NONCE_BYTES);
        $cipher = sodium_crypto_secretbox($plaintext, $nonce, $this->key);

        return sodium_bin2base64($nonce . $cipher, \SODIUM_BASE64_VARIANT_ORIGINAL);
    }

    /**
     * Decrypt a stored ciphertext. Returns null for empty/absent values.
     */
    public function decrypt(?string $ciphertext): ?string
    {
        if ($ciphertext === null || $ciphertext === '') {
            return null;
        }

        try {
            $decoded = sodium_base642bin($ciphertext, \SODIUM_BASE64_VARIANT_ORIGINAL);
        } catch (\SodiumException) {
            throw new InvalidArgumentException('Invalid PII ciphertext encoding.');
        }

        if ($decoded === false) {
            throw new InvalidArgumentException('Invalid PII ciphertext encoding.');
        }

        if (strlen($decoded) < self::NONCE_BYTES + self::MAC_BYTES) {
            throw new InvalidArgumentException('Invalid PII ciphertext payload.');
        }

        $nonce  = substr($decoded, 0, self::NONCE_BYTES);
        $cipher = substr($decoded, self::NONCE_BYTES);

        $plaintext = sodium_crypto_secretbox_open($cipher, $nonce, $this->key);

        if ($plaintext === false) {
            throw new RuntimeException('PII decryption failed.');
        }

        return $plaintext;
    }

    /**
     * Decrypt a cipher column from a row/array safely when select() omitted the field.
     */
    public function decryptFromRow(array $row, string $cipherField): ?string
    {
        if (! array_key_exists($cipherField, $row)) {
            return null;
        }

        return $this->decrypt($row[$cipherField]);
    }

    /**
     * Normalize a WhatsApp / phone number for lookup hashing.
     */
    public function normalizePhone(string $phone): string
    {
        $normalized = preg_replace('/[\s\-\.\(\)]+/', '', trim($phone)) ?? '';

        if (str_starts_with($normalized, '+62')) {
            return '0' . substr($normalized, 3);
        }

        if (str_starts_with($normalized, '62') && strlen($normalized) > 2) {
            return '0' . substr($normalized, 2);
        }

        return $normalized;
    }

    /**
     * Normalize an email address for lookup hashing.
     */
    public function normalizeEmail(string $email): string
    {
        return strtolower(trim($email));
    }

    /**
     * HMAC-SHA256 lookup hash of an already-normalized value (64-char hex).
     */
    public function hashLookup(string $normalized): string
    {
        return hash_hmac('sha256', $normalized, $this->lookupKey);
    }

    /**
     * Hash a phone number after normalization.
     */
    public function hashPhone(string $phone): string
    {
        return $this->hashLookup($this->normalizePhone($phone));
    }

    /**
     * Hash an email address after normalization.
     */
    public function hashEmail(string $email): string
    {
        return $this->hashLookup($this->normalizeEmail($email));
    }

    /**
     * Compare a raw phone input against a stored lookup hash.
     */
    public function verifyPhoneHash(string $phone, string $storedHash): bool
    {
        return hash_equals($storedHash, $this->hashPhone($phone));
    }

    /**
     * Compare a raw email input against a stored lookup hash.
     */
    public function verifyEmailHash(string $email, string $storedHash): bool
    {
        return hash_equals($storedHash, $this->hashEmail($email));
    }

    private function decodeKey(string $keyMaterial): string
    {
        $decoded = base64_decode($keyMaterial, true);

        if ($decoded !== false && strlen($decoded) === self::KEY_BYTES) {
            return $decoded;
        }

        if (strlen($keyMaterial) === self::KEY_BYTES) {
            return $keyMaterial;
        }

        throw new InvalidArgumentException(
            'pii.key must be a base64-encoded 32-byte key or a raw 32-byte string.',
        );
    }
}
