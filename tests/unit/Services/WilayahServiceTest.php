<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\DTOs\Lingkungan\LingkunganDto;
use App\DTOs\Wilayah\WilayahDto;
use App\Entities\Lingkungan;
use App\Entities\Wilayah;
use App\Libraries\PiiCipher;
use App\Repositories\LingkunganRepository;
use App\Repositories\WilayahRepository;
use App\Services\LingkunganService;
use App\Services\WilayahService;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class WilayahServiceTest extends CIUnitTestCase
{
    private PiiCipher $piiCipher;

    protected function setUp(): void
    {
        parent::setUp();

        if (! extension_loaded('sodium')) {
            $this->markTestSkipped('The sodium extension is required for WilayahService tests.');
        }

        $this->piiCipher = new PiiCipher(
            sodium_bin2base64(random_bytes(32), \SODIUM_BASE64_VARIANT_ORIGINAL),
        );
    }

    public function testCreateWilayahPassesEncryptedContactToRepository(): void
    {
        $plaintext = '081234567890';
        $captured  = [];

        $repository = $this->createStub(WilayahRepository::class);
        $repository->method('create')->willReturnCallback(static function (array $data) use (&$captured): int {
            $captured = $data;

            return 1;
        });

        $service = new WilayahService($repository, $this->piiCipher);
        $service->create(new WilayahDto(
            nama: 'Wilayah Alpha',
            ketuaNama: 'Budi Santoso',
            ketuaKontak: $plaintext,
        ));

        $this->assertArrayHasKey('ketua_kontak_cipher', $captured);
        $this->assertArrayHasKey('ketua_kontak_hash', $captured);
        $this->assertNotSame($plaintext, $captured['ketua_kontak_cipher']);
        $this->assertStringNotContainsString($plaintext, (string) $captured['ketua_kontak_cipher']);
        $this->assertSame(64, strlen((string) $captured['ketua_kontak_hash']));
        $this->assertSame($plaintext, $this->piiCipher->decrypt((string) $captured['ketua_kontak_cipher']));
    }

    public function testUpdateWilayahReEncryptsContactWhenChanged(): void
    {
        $original  = '081234567890';
        $updated   = '0812999888777';
        $captured  = [];

        $existing = new Wilayah([
            'id'                  => 1,
            'nama'                => 'Wilayah Beta',
            'ketua_nama'          => 'Ani Wijaya',
            'ketua_kontak_cipher' => $this->piiCipher->encrypt($original),
            'ketua_kontak_hash'   => $this->piiCipher->hashPhone($original),
        ]);

        $repository = $this->createStub(WilayahRepository::class);
        $repository->method('find')->willReturn($existing);
        $repository->method('update')->willReturnCallback(static function (int|string $id, array $data) use (&$captured): bool {
            $captured = $data;

            return true;
        });

        $service = new WilayahService($repository, $this->piiCipher);
        $service->update(1, new WilayahDto(
            nama: 'Wilayah Beta',
            ketuaNama: 'Ani Wijaya',
            ketuaKontak: $updated,
        ));

        $this->assertNotSame($updated, $captured['ketua_kontak_cipher']);
        $this->assertStringNotContainsString($updated, (string) $captured['ketua_kontak_cipher']);
        $this->assertSame($updated, $this->piiCipher->decrypt((string) $captured['ketua_kontak_cipher']));
    }

    public function testCreateLingkunganPassesEncryptedContactToRepository(): void
    {
        $plaintext = '0812111222333';
        $captured  = [];

        $wilayahRepository = $this->createStub(WilayahRepository::class);
        $wilayahRepository->method('find')->willReturn(new Wilayah(['id' => 1]));

        $lingkunganRepository = $this->createStub(LingkunganRepository::class);
        $lingkunganRepository->method('create')->willReturnCallback(static function (array $data) use (&$captured): int {
            $captured = $data;

            return 1;
        });

        $service = new LingkunganService(
            $lingkunganRepository,
            $wilayahRepository,
            $this->piiCipher,
        );

        $service->create(new LingkunganDto(
            wilayahId: 1,
            nama: 'Lingkungan Satu',
            ketuaNama: 'Citra Dewi',
            ketuaKontak: $plaintext,
        ));

        $this->assertNotSame($plaintext, $captured['ketua_kontak_cipher']);
        $this->assertStringNotContainsString($plaintext, (string) $captured['ketua_kontak_cipher']);
        $this->assertSame($plaintext, $this->piiCipher->decrypt((string) $captured['ketua_kontak_cipher']));
    }

    public function testFindAllForListUsesRepositoryWithoutCipherColumns(): void
    {
        $listItem = new Wilayah([
            'id'         => 1,
            'nama'       => 'Wilayah Delta',
            'ketua_nama' => 'Doni Pratama',
        ]);

        $repository = $this->createStub(WilayahRepository::class);
        $repository->method('findAllForList')->willReturn([$listItem]);

        $service = new WilayahService($repository, $this->piiCipher);
        $items   = $service->findAllForList();

        $this->assertCount(1, $items);
        $this->assertFalse(isset($items[0]->ketua_kontak_cipher));
        $this->assertFalse(isset($items[0]->ketua_kontak_hash));
    }

    public function testGetDetailDecryptsContactOnlyInAuthorizedFlow(): void
    {
        $plaintext = '0812555666777';
        $wilayah   = new Wilayah([
            'id'                  => 1,
            'nama'                => 'Wilayah Epsilon',
            'ketua_nama'          => 'Eko Prasetyo',
            'ketua_kontak_cipher' => $this->piiCipher->encrypt($plaintext),
            'ketua_kontak_hash'   => $this->piiCipher->hashPhone($plaintext),
        ]);

        $repository = $this->createStub(WilayahRepository::class);
        $repository->method('getWithLingkungan')->willReturn(
            new \App\DTOs\Wilayah\WilayahWithLingkunganDto(
                wilayah: $wilayah,
                lingkungan: [],
            ),
        );

        $service = new WilayahService($repository, $this->piiCipher);
        $detail  = $service->getDetail(1);

        $this->assertSame($plaintext, $detail->ketuaKontak);
    }

    public function testLingkunganGetDetailDecryptsOptionalContact(): void
    {
        $plaintext = '0812777888999';
        $lingkungan = new Lingkungan([
            'id'                  => 1,
            'wilayah_id'          => 1,
            'nama'                => 'Lingkungan Dua',
            'ketua_nama'          => 'Fajar Nugroho',
            'ketua_kontak_cipher' => $this->piiCipher->encrypt($plaintext),
            'ketua_kontak_hash'   => $this->piiCipher->hashPhone($plaintext),
        ]);

        $lingkunganRepository = $this->createStub(LingkunganRepository::class);
        $lingkunganRepository->method('find')->willReturn($lingkungan);

        $service = new LingkunganService(
            $lingkunganRepository,
            $this->createStub(WilayahRepository::class),
            $this->piiCipher,
        );

        $detail = $service->getDetail(1);

        $this->assertSame($plaintext, $detail->ketuaKontak);
    }
}
