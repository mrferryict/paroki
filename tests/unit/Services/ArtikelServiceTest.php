<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Enums\PublishStatus;
use App\Libraries\SlugGenerator;
use App\Repositories\ArtikelRepository;
use App\Services\ArtikelKategoriService;
use App\Services\ArtikelService;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class ArtikelServiceTest extends CIUnitTestCase
{
    public function testBuildAdminDtoGeneratesSlugForPublishedArticle(): void
    {
        $repository = $this->createStub(ArtikelRepository::class);
        $repository->method('slugExists')->willReturn(false);

        $kategoriService = $this->createStub(ArtikelKategoriService::class);

        $service = new ArtikelService($repository, new SlugGenerator(), $kategoriService);
        $dto     = $service->buildAdminDto(
            judul: 'Renungan Minggu',
            kategori: 'renungan_harian',
            konten: 'Konten artikel',
            status: PublishStatus::Terbit,
            tanggalTerbitRaw: null,
        );

        $this->assertSame('renungan-minggu', $dto->slug);
        $this->assertSame('renungan_harian', $dto->kategori);
        $this->assertNotNull($dto->tanggalTerbit);
    }
}
