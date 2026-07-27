<?php

declare(strict_types=1);

namespace Tests\Unit\Libraries;

use App\Libraries\PiiCipher;
use CodeIgniter\Test\CIUnitTestCase;
use InvalidArgumentException;
use RuntimeException;

/**
 * @internal
 */
final class PiiCipherTest extends CIUnitTestCase
{
    private string $keyMaterial;

    private PiiCipher $cipher;

    protected function setUp(): void
    {
        parent::setUp();

        if (! extension_loaded('sodium')) {
            $this->markTestSkipped('The sodium extension is required for PiiCipher tests.');
        }

        $this->keyMaterial = sodium_bin2base64(random_bytes(32), \SODIUM_BASE64_VARIANT_ORIGINAL);
        $this->cipher      = new PiiCipher($this->keyMaterial);
    }

    public function testEncryptDecryptRoundTrip(): void
    {
        $plaintext = '081234567890';

        $ciphertext = $this->cipher->encrypt($plaintext);

        $this->assertNotSame($plaintext, $ciphertext);
        $this->assertSame($plaintext, $this->cipher->decrypt($ciphertext));
    }

    public function testEncryptProducesDifferentCiphertextEachTime(): void
    {
        $first  = $this->cipher->encrypt('081234567890');
        $second = $this->cipher->encrypt('081234567890');

        $this->assertNotSame($first, $second);
        $this->assertSame('081234567890', $this->cipher->decrypt($first));
        $this->assertSame('081234567890', $this->cipher->decrypt($second));
    }

    public function testDecryptReturnsNullForEmptyValues(): void
    {
        $this->assertNull($this->cipher->decrypt(null));
        $this->assertNull($this->cipher->decrypt(''));
    }

    public function testDecryptFromRowReturnsNullWhenCipherColumnMissing(): void
    {
        $row = ['nama_lengkap' => 'Budi Santoso'];

        $this->assertNull($this->cipher->decryptFromRow($row, 'whatsapp_cipher'));
    }

    public function testDecryptFromRowDecryptsWhenCipherColumnPresent(): void
    {
        $plaintext  = '081298765432';
        $ciphertext = $this->cipher->encrypt($plaintext);

        $row = [
            'nama_lengkap'    => 'Budi Santoso',
            'whatsapp_cipher' => $ciphertext,
        ];

        $this->assertSame($plaintext, $this->cipher->decryptFromRow($row, 'whatsapp_cipher'));
    }

    public function testDecryptFromRowReturnsNullWhenCipherValueIsNull(): void
    {
        $row = ['whatsapp_cipher' => null];

        $this->assertNull($this->cipher->decryptFromRow($row, 'whatsapp_cipher'));
    }

    public function testPhoneNormalizationBeforeHash(): void
    {
        $variants = [
            '0812-3456-7890',
            '0812 3456 7890',
            '+62 812-3456-7890',
            '6281234567890',
        ];

        $expected = $this->cipher->hashPhone('081234567890');

        foreach ($variants as $variant) {
            $this->assertSame($expected, $this->cipher->hashPhone($variant), "Failed for variant: {$variant}");
        }
    }

    public function testHashLookupIsDeterministic(): void
    {
        $hashA = $this->cipher->hashPhone('081234567890');
        $hashB = $this->cipher->hashPhone('081234567890');

        $this->assertSame($hashA, $hashB);
        $this->assertSame(64, strlen($hashA));
    }

    public function testVerifyPhoneHashMatchesStoredLookup(): void
    {
        $storedHash = $this->cipher->hashPhone('0812-3456-7890');

        $this->assertTrue($this->cipher->verifyPhoneHash('081234567890', $storedHash));
        $this->assertFalse($this->cipher->verifyPhoneHash('081299999999', $storedHash));
    }

    public function testEmailHashLookupAndVerification(): void
    {
        $storedHash = $this->cipher->hashEmail('Admin@Paroki.example ');

        $this->assertSame(64, strlen($storedHash));
        $this->assertTrue($this->cipher->verifyEmailHash('admin@paroki.example', $storedHash));
        $this->assertFalse($this->cipher->verifyEmailHash('other@paroki.example', $storedHash));
    }

    public function testDifferentPlaintextsProduceDifferentHashes(): void
    {
        $phoneHash = $this->cipher->hashPhone('081234567890');
        $otherHash = $this->cipher->hashPhone('081299999999');

        $this->assertNotSame($phoneHash, $otherHash);
    }

    public function testThrowsWhenKeyIsMissing(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('pii.key is not configured');

        new PiiCipher('');
    }

    public function testThrowsWhenKeyMaterialIsInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('pii.key must be a base64-encoded 32-byte key');

        new PiiCipher('not-a-valid-key');
    }

    public function testThrowsWhenCiphertextIsMalformed(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->cipher->decrypt('not-valid-base64-ciphertext!!!');
    }
}
