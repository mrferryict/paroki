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

    public function findPublishedPaginated(?string $kategori, int $page, int $perPage = 12): PaginatedResultDto
    {
        return $this->findPaginated(new ContentListFilterDto(
            kategori: $kategori !== '' ? $kategori : null,
            status: PublishStatus::Terbit->value,
            page: max(1, $page),
            perPage: $perPage,
        ));
    }

    /**
     * @return array<string, mixed>
     */
    public function mapForPublicCard(Artikel $item): array
    {
        $kategori = ArtikelKategori::tryFromString((string) ($item->kategori ?? ''));

        $excerpt = strip_tags((string) ($item->konten ?? ''));

        if (mb_strlen($excerpt) > 140) {
            $excerpt = mb_substr($excerpt, 0, 137) . '…';
        }

        $kategoriValue = $kategori?->value ?? (string) ($item->kategori ?? '');

        return [
            'id'            => (int) $item->id,
            'judul'         => (string) ($item->judul ?? ''),
            'slug'          => (string) ($item->slug ?? ''),
            'kategori'      => $kategoriValue,
            'kategoriLabel' => $kategori?->label() ?? $kategoriValue,
            'ringkasan'     => $excerpt,
            'tanggalTerbit' => $this->formatPublicDate((string) ($item->tanggal_terbit ?? '')),
            'href'          => site_url('katekese/' . $kategoriValue . '/' . ($item->slug ?? '')),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function mapForPublicDetail(Artikel $item): array
    {
        return array_merge($this->mapForPublicCard($item), [
            'konten' => (string) ($item->konten ?? ''),
        ]);
    }

    public function findPublishedByKategoriAndSlug(string $kategori, string $slug): Artikel
    {
        if (ArtikelKategori::tryFromString($kategori) === null) {
            throw new DomainException('Artikel tidak ditemukan.');
        }

        $artikel = $this->findBySlug($slug);

        $artikelKategori = (string) ($artikel->kategori ?? '');

        if ($artikelKategori !== $kategori) {
            throw new DomainException('Artikel tidak ditemukan.');
        }

        return $artikel;
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

    public function buildAdminDto(
        string $judul,
        ArtikelKategori $kategori,
        ?string $konten,
        PublishStatus $status,
        ?string $tanggalTerbitRaw,
        ?int $excludeId = null,
    ): ArtikelDto {
        return new ArtikelDto(
            judul: $judul,
            slug: $this->generateUniqueSlug($judul, $excludeId),
            kategori: $kategori,
            konten: $konten,
            status: $status,
            tanggalTerbit: $this->resolveTanggalTerbit($status, $tanggalTerbitRaw),
        );
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

    private function formatPublicDate(string $raw): string
    {
        if ($raw === '') {
            return '';
        }

        return Time::parse($raw, null, 'id_ID')->toLocalizedString('d MMM yyyy');
    }
}
