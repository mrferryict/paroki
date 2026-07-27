<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Entities\Lingkungan;
use App\Libraries\PiiCipher;
use App\Repositories\LingkunganRepository;
use App\Repositories\WilayahRepository;
use App\Services\LingkunganService;
use CodeIgniter\Test\CIUnitTestCase;
use DomainException;

/**
 * @internal
 */
final class LingkunganServiceTest extends CIUnitTestCase
{
    public function testGetDetailForWilayahRejectsMismatchedWilayah(): void
    {
        $lingkungan = new Lingkungan([
            'id'         => 5,
            'wilayah_id' => 2,
            'nama'       => 'Lingkungan B',
            'ketua_nama' => 'Ani',
        ]);

        $lingkunganRepository = $this->createStub(LingkunganRepository::class);
        $lingkunganRepository->method('findByIdForDetail')->willReturn($lingkungan);

        $service = new LingkunganService(
            $lingkunganRepository,
            $this->createStub(WilayahRepository::class),
            $this->createStub(PiiCipher::class),
        );

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Lingkungan tidak ditemukan di wilayah ini.');

        $service->getDetailForWilayah(wilayahId: 1, id: 5);
    }
}
