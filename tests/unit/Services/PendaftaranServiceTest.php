<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\DTOs\Pendaftaran\PendaftaranDto;
use App\Libraries\PiiCipher;
use App\Repositories\PendaftaranRepository;
use App\Services\PendaftaranService;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class PendaftaranServiceTest extends CIUnitTestCase
{
    private PiiCipher $piiCipher;

    protected function setUp(): void
    {
        parent::setUp();

        if (! extension_loaded('sodium')) {
            $this->markTestSkipped('The sodium extension is required for PendaftaranService tests.');
        }

        $this->piiCipher = new PiiCipher(
            sodium_bin2base64(random_bytes(32), \SODIUM_BASE64_VARIANT_ORIGINAL),
        );
    }

    public function testSubmitPassesEncryptedWhatsappToRepository(): void
    {
        $plaintext = '081234567890';
        $captured  = [];

        $repository = $this->createStub(PendaftaranRepository::class);
        $repository->method('create')->willReturnCallback(static function (array $data) use (&$captured): int {
            $captured = $data;

            return 1;
        });

        $service = new PendaftaranService(
            $repository,
            $this->piiCipher,
            new \App\Models\SakramenJenisModel(),
        );

        $service->submit(new PendaftaranDto(
            namaLengkap: 'Budi Santoso',
            whatsapp: $plaintext,
            sakramenJenisId: null,
            pesan: 'Permohonan baptis',
        ));

        $this->assertSame('Budi Santoso', $captured['nama_lengkap']);
        $this->assertArrayHasKey('whatsapp_cipher', $captured);
        $this->assertArrayHasKey('whatsapp_hash', $captured);
        $this->assertNotSame($plaintext, $captured['whatsapp_cipher']);
        $this->assertStringNotContainsString($plaintext, (string) $captured['whatsapp_cipher']);
        $this->assertSame($plaintext, $this->piiCipher->decrypt((string) $captured['whatsapp_cipher']));
    }
}
