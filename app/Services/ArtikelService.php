<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Artikel\ArtikelDto;
use App\DTOs\Shared\ContentListFilterDto;
use App\DTOs\Shared\PaginatedResultDto;
use App\Entities\Artikel;
use App\Enums\ArtikelKategori;
use App\Enums\PublishStatus;
use App\Libraries\SlugGenerator;
use App\Repositories\ArtikelRepository;
use CodeIgniter\I18n\Time;
use DomainException;
use RuntimeException;

class ArtikelService
{
    public function __construct(
        private readonly ArtikelRepository $artikelRepository,
        private readonly SlugGenerator $slugGenerator,
    ) {}

    /**
     * @return array<string, string>
     */
    public function kategoriOptions(): array
    {
        return ArtikelKategori::options();
    }

    /**
     * @return array<string, string>
     */
    public function statusOptions(): array
    {
        return PublishStatus::options();
    }

    public function findPaginated(ContentListFilterDto $filter): PaginatedResultDto
    {
        return $this->artikelRepository->findPaginated($filter);
    }

    /**
     * @return list<Artikel>
     */
    public function findLatestPublished(?string $kategori = null, int $limit = 8): array
    {
        return $this->artikelRepository->findLatestPublished($kategori, $limit);
    }

    public function findBySlug(string $slug): Artikel
    {
        $artikel = $this->artikelRepository->findBySlug($slug);

        if ($artikel === null || $artikel->status !== PublishStatus::Terbit->value) {
            throw new DomainException('Artikel tidak ditemukan.');
        }

        return $artikel;
    }

    public function findById(int $id): Artikel
    {
        $artikel = $this->artikelRepository->find($id);

        if ($artikel === null) {
            throw new DomainException('Artikel tidak ditemukan.');
        }

        /** @var Artikel $artikel */
        return $artikel;
    }

    public function create(ArtikelDto $dto): int
    {
        $id = $this->artikelRepository->create($dto->toModelData());

        if ($id === false) {
            throw new RuntimeException('Gagal menyimpan artikel.');
        }

        return (int) $id;
    }

    public function update(int $id, ArtikelDto $dto): void
    {
        if ($this->artikelRepository->find($id) === null) {
            throw new DomainException('Artikel tidak ditemukan.');
        }

        if (! $this->artikelRepository->update($id, $dto->toModelData())) {
            throw new RuntimeException('Gagal memperbarui artikel.');
        }
    }

    public function delete(int $id): void
    {
        if ($this->artikelRepository->find($id) === null) {
            throw new DomainException('Artikel tidak ditemukan.');
        }

        if (! $this->artikelRepository->delete($id)) {
            throw new RuntimeException('Gagal menghapus artikel.');
        }
    }

    public function generateUniqueSlug(string $judul, ?int $excludeId = null): string
    {
        return $this->slugGenerator->unique(
            $judul,
            fn (string $slug, ?int $exclude): bool => $this->artikelRepository->slugExists($slug, $exclude),
            $excludeId,
        );
    }

    public function resolveTanggalTerbit(PublishStatus $status, ?string $rawValue): ?Time
    {
        if ($status === PublishStatus::Terbit) {
            if ($rawValue === null || trim($rawValue) === '') {
                return Time::now();
            }

            return Time::parse(trim($rawValue));
        }

        if ($rawValue === null || trim($rawValue) === '') {
            return null;
        }

        return Time::parse(trim($rawValue));
    }
}
