<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Enums\BeritaKategori;
use App\Enums\PublishStatus;
use App\Libraries\SlugGenerator;
use App\Repositories\BeritaRepository;
use App\Services\BeritaService;
use CodeIgniter\Test\CIUnitTestCase;
use InvalidArgumentException;

/**
 * @internal
 */
final class BeritaServiceTest extends CIUnitTestCase
{
    public function testFindLatestPublishedDelegatesToRepository(): void
    {
        $repository = $this->createStub(BeritaRepository::class);
        $repository->method('findLatestPublished')->with(4)->willReturn([]);

        $service = new BeritaService($repository, new SlugGenerator());

        $this->assertSame([], $service->findLatestPublished(limit: 4));
    }

    public function testBuildAdminDtoGeneratesSlugAndPublishDate(): void
    {
        $repository = $this->createStub(BeritaRepository::class);
        $repository->method('slugExists')->willReturn(false);

        $service = new BeritaService($repository, new SlugGenerator());
        $dto     = $service->buildAdminDto(
            judul: 'Misa Paroki',
            kategori: BeritaKategori::Pengumuman,
            ringkasan: 'Ringkasan',
            konten: null,
            status: PublishStatus::Terbit,
            tanggalTerbitRaw: null,
            gambarUtama: 'uploads/berita/x.jpg',
        );

        $this->assertSame('misa-paroki', $dto->slug);
        $this->assertSame(PublishStatus::Terbit, $dto->status);
        $this->assertNotNull($dto->tanggalTerbit);
    }

    public function testResolveUploadedImageThrowsWhenRequiredFileMissing(): void
    {
        $service = new BeritaService(
            $this->createStub(BeritaRepository::class),
            new SlugGenerator(),
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Gambar utama wajib diunggah.');

        $service->resolveUploadedImage(null, required: true);
    }

    public function testResolveUploadedImageForUpdateKeepsExistingPathWhenNoUpload(): void
    {
        $service = new BeritaService(
            $this->createStub(BeritaRepository::class),
            new SlugGenerator(),
        );

        $path = $service->resolveUploadedImageForUpdate(null, 'uploads/berita/existing.jpg');

        $this->assertSame('uploads/berita/existing.jpg', $path);
    }
}
