<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Artikel\ArtikelDto;
use App\DTOs\Berita\BeritaDto;
use App\DTOs\Shared\ContentListFilterDto;
use App\DTOs\Shared\PaginatedResultDto;
use App\Entities\Berita;
use App\Enums\BeritaKategori;
use App\Enums\PublishStatus;
use App\Libraries\SlugGenerator;
use App\Repositories\BeritaRepository;
use CodeIgniter\HTTP\Files\UploadedFile;
use CodeIgniter\I18n\Time;
use DomainException;
use InvalidArgumentException;
use RuntimeException;

class BeritaService
{
    private const UPLOAD_SUBDIR = 'uploads/berita';

    /** @var list<string> */
    private const ALLOWED_IMAGE_MIMES = [
        'image/jpeg',
        'image/png',
        'image/webp',
    ];

    public function __construct(
        private readonly BeritaRepository $beritaRepository,
        private readonly SlugGenerator $slugGenerator,
    ) {}

    /**
     * @return array<string, string>
     */
    public function kategoriOptions(): array
    {
        return BeritaKategori::options();
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
        return $this->beritaRepository->findPaginated($filter);
    }

    /**
     * @return list<Berita>
     */
    public function findLatestPublished(int $limit = 6): array
    {
        return $this->beritaRepository->findLatestPublished($limit);
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
    public function mapForPublicCard(Berita $item): array
    {
        $kategori = BeritaKategori::tryFromString((string) ($item->kategori ?? ''));

        return [
            'id'            => (int) $item->id,
            'judul'         => (string) ($item->judul ?? ''),
            'slug'          => (string) ($item->slug ?? ''),
            'kategori'      => $kategori?->value ?? (string) ($item->kategori ?? ''),
            'kategoriLabel' => $kategori?->label() ?? (string) ($item->kategori ?? ''),
            'ringkasan'     => (string) ($item->ringkasan ?? ''),
            'gambar'        => $this->resolvePublicImage((string) ($item->gambar_utama ?? '')),
            'tanggalTerbit' => $this->formatPublicDate((string) ($item->tanggal_terbit ?? '')),
            'href'          => site_url('berita/' . ($item->slug ?? '')),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function mapForPublicDetail(Berita $item): array
    {
        return array_merge($this->mapForPublicCard($item), [
            'konten' => (string) ($item->konten ?? ''),
        ]);
    }

    public function findBySlug(string $slug): Berita
    {
        $berita = $this->beritaRepository->findBySlug($slug);

        if ($berita === null || $berita->status !== PublishStatus::Terbit->value) {
            throw new DomainException('Berita tidak ditemukan.');
        }

        return $berita;
    }

    public function findById(int $id): Berita
    {
        $berita = $this->beritaRepository->find($id);

        if ($berita === null) {
            throw new DomainException('Berita tidak ditemukan.');
        }

        /** @var Berita $berita */
        return $berita;
    }

    public function create(BeritaDto $dto): int
    {
        $id = $this->beritaRepository->create($dto->toModelData());

        if ($id === false) {
            throw new RuntimeException('Gagal menyimpan berita.');
        }

        return (int) $id;
    }

    public function buildAdminDto(
        string $judul,
        BeritaKategori $kategori,
        ?string $ringkasan,
        ?string $konten,
        PublishStatus $status,
        ?string $tanggalTerbitRaw,
        ?string $gambarUtama,
        ?int $excludeId = null,
    ): BeritaDto {
        return new BeritaDto(
            judul: $judul,
            slug: $this->generateUniqueSlug($judul, $excludeId),
            kategori: $kategori,
            ringkasan: $ringkasan,
            konten: $konten,
            gambarUtama: $gambarUtama,
            status: $status,
            tanggalTerbit: $this->resolveTanggalTerbit($status, $tanggalTerbitRaw),
        );
    }

    public function resolveUploadedImage(?UploadedFile $file, bool $required): ?string
    {
        if ($file === null || ! $file->isValid() || $file->hasMoved()) {
            if ($required) {
                throw new InvalidArgumentException('Gambar utama wajib diunggah.');
            }

            return null;
        }

        return $this->storeUploadedImage($file);
    }

    public function resolveUploadedImageForUpdate(?UploadedFile $file, string $existingPath): string
    {
        if ($file !== null && $file->isValid() && ! $file->hasMoved()) {
            return $this->storeUploadedImage($file);
        }

        return $existingPath;
    }

    public function update(int $id, BeritaDto $dto): void
    {
        $existing = $this->findById($id);
        $oldGambar = (string) ($existing->gambar_utama ?? '');

        if (! $this->beritaRepository->update($id, $dto->toModelData())) {
            throw new RuntimeException('Gagal memperbarui berita.');
        }

        if ($oldGambar !== '' && $oldGambar !== (string) ($dto->gambarUtama ?? '')) {
            $this->removeImageIfUnused($oldGambar);
        }
    }

    public function delete(int $id): void
    {
        $existing = $this->findById($id);

        if (! $this->beritaRepository->delete($id)) {
            throw new RuntimeException('Gagal menghapus berita.');
        }

        $this->removeImageIfUnused((string) ($existing->gambar_utama ?? ''));
    }

    public function generateUniqueSlug(string $judul, ?int $excludeId = null): string
    {
        return $this->slugGenerator->unique(
            $judul,
            fn (string $slug, ?int $exclude): bool => $this->beritaRepository->slugExists($slug, $exclude),
            $excludeId,
        );
    }

    public function storeUploadedImage(UploadedFile $file): string
    {
        if (! $file->isValid()) {
            throw new InvalidArgumentException($file->getErrorString() ?: 'File gambar tidak valid.');
        }

        $mime = $file->getMimeType();

        if ($mime === null || ! in_array($mime, self::ALLOWED_IMAGE_MIMES, true)) {
            throw new InvalidArgumentException('Format gambar harus JPEG, PNG, atau WebP.');
        }

        $targetDir = FCPATH . self::UPLOAD_SUBDIR;

        if (! is_dir($targetDir) && ! mkdir($targetDir, 0755, true) && ! is_dir($targetDir)) {
            throw new RuntimeException('Direktori unggahan berita tidak dapat dibuat.');
        }

        $storedName = $file->getRandomName();

        if (! $file->move($targetDir, $storedName)) {
            throw new RuntimeException('Gagal mengunggah gambar berita.');
        }

        return self::UPLOAD_SUBDIR . '/' . $storedName;
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

    private function removeImageIfUnused(string $relativePath): void
    {
        if ($relativePath === '') {
            return;
        }

        $model = model(\App\Models\BeritaModel::class);

        if ($model->where('gambar_utama', $relativePath)->countAllResults() > 0) {
            return;
        }

        $fullPath = FCPATH . ltrim($relativePath, '/');

        if (is_file($fullPath)) {
            unlink($fullPath);
        }
    }

    private function formatPublicDate(string $raw): string
    {
        if ($raw === '') {
            return '';
        }

        return Time::parse($raw, null, 'id_ID')->toLocalizedString('d MMM yyyy');
    }

    private function resolvePublicImage(string $relativePath): string
    {
        if ($relativePath === '') {
            return '';
        }

        return base_url($relativePath);
    }
}
