<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Dokumen\DokumenDownloadDto;
use App\DTOs\Dokumen\DokumenDto;
use App\Entities\Dokumen;
use App\Models\DokumenModel;
use CodeIgniter\HTTP\Files\UploadedFile;
use DomainException;
use InvalidArgumentException;
use RuntimeException;

class DokumenService
{
    private const UPLOAD_SUBDIR = 'uploads/dokumen';

    /** @var list<string> */
    private const ALLOWED_MIMES = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'image/jpeg',
        'image/png',
    ];

    public function __construct(
        private readonly DokumenModel $dokumenModel,
        private readonly DokumenKategoriService $dokumenKategoriService,
    ) {}

    /**
     * @return list<Dokumen>
     */
    public function findAllOrdered(): array
    {
        /** @var list<Dokumen> */
        return $this->dokumenModel
            ->select('id, nama, kategori, download_count, created_at, updated_at')
            ->orderBy('kategori', 'ASC')
            ->orderBy('nama', 'ASC')
            ->findAll();
    }

    /**
     * @return list<Dokumen>
     */
    public function findAllForPublic(?string $kategori = null): array
    {
        $activeSlugs = $this->dokumenKategoriService->activeSlugList();

        if ($activeSlugs === []) {
            return [];
        }

        $builder = $this->dokumenModel
            ->select('id, nama, kategori, created_at, updated_at')
            ->whereIn('kategori', $activeSlugs)
            ->orderBy('kategori', 'ASC')
            ->orderBy('nama', 'ASC');

        if ($kategori !== null && $kategori !== '') {
            $builder->where('kategori', $kategori);
        }

        /** @var list<Dokumen> */
        return $builder->findAll();
    }

    /**
     * @return array<string, string>
     */
    public function kategoriOptions(): array
    {
        return $this->dokumenKategoriService->kategoriOptions();
    }

    /**
     * @return array<string, string>
     */
    public function kategoriOptionsForAdmin(?string $currentSlug = null): array
    {
        return $this->dokumenKategoriService->kategoriOptionsForAdmin($currentSlug);
    }

    /**
     * @return array<string, string>
     */
    public function allKategoriLabels(): array
    {
        return $this->dokumenKategoriService->allKategoriLabels();
    }

    /**
     * @return array<string, mixed>
     */
    public function mapForPublic(Dokumen $item): array
    {
        $slug = (string) ($item->kategori ?? '');

        return [
            'id'            => (int) $item->id,
            'nama'          => (string) ($item->nama ?? ''),
            'kategori'      => $slug,
            'kategoriLabel' => $this->dokumenKategoriService->getLabelForSlug($slug) ?? $slug,
            'downloadUrl'   => $this->publicDownloadUrl((int) $item->id),
        ];
    }

    public function findById(int $id): Dokumen
    {
        $item = $this->dokumenModel->find($id);

        if ($item === null) {
            throw new DomainException('Unduhan tidak ditemukan.');
        }

        return $item;
    }

    public function create(DokumenDto $dto): int
    {
        if (! in_array($dto->kategori, $this->dokumenKategoriService->activeSlugList(), true)) {
            throw new InvalidArgumentException('Kategori unduhan tidak valid atau tidak aktif.');
        }

        $id = $this->dokumenModel->insert($dto->toModelData());

        if ($id === false) {
            throw new RuntimeException('Gagal menyimpan unduhan.');
        }

        return (int) $id;
    }

    public function update(int $id, DokumenDto $dto): void
    {
        $existing = $this->findById($id);
        $allowed  = $this->dokumenKategoriService->kategoriOptionsForAdmin((string) ($existing->kategori ?? ''));

        if (! array_key_exists($dto->kategori, $allowed)) {
            throw new InvalidArgumentException('Kategori unduhan tidak valid.');
        }

        if (! $this->dokumenModel->update($id, $dto->toModelData())) {
            throw new RuntimeException('Gagal memperbarui unduhan.');
        }

        $oldPath = (string) $existing->file_path;

        if ($oldPath !== '' && $oldPath !== $dto->filePath) {
            $this->removeStoredFile($oldPath);
        }
    }

    public function delete(int $id): void
    {
        $existing = $this->findById($id);

        if (! $this->dokumenModel->delete($id)) {
            throw new RuntimeException('Gagal menghapus unduhan.');
        }

        $this->removeStoredFile((string) $existing->file_path);
    }

    public function resolveDownload(int $id): DokumenDownloadDto
    {
        $dokumen = $this->findById($id);
        $slug    = (string) ($dokumen->kategori ?? '');

        if (! $this->dokumenKategoriService->isActiveSlug($slug)) {
            throw new DomainException('Unduhan tidak tersedia.');
        }

        $fullPath = $this->fullPathFromRelative((string) $dokumen->file_path);

        if (! is_file($fullPath)) {
            throw new DomainException('Berkas unduhan tidak ditemukan di server.');
        }

        $this->incrementDownloadCount($id);

        $extension  = pathinfo($fullPath, PATHINFO_EXTENSION);
        $clientName = (string) $dokumen->nama;

        if ($extension !== '' && ! str_ends_with(strtolower($clientName), '.' . strtolower($extension))) {
            $clientName .= '.' . $extension;
        }

        return new DokumenDownloadDto(
            fullPath: $fullPath,
            clientName: $clientName,
        );
    }

    public function incrementDownloadCount(int $id): void
    {
        $this->dokumenModel
            ->where('id', $id)
            ->set('download_count', 'download_count + 1', false)
            ->update();
    }

    public function storeUploadedFile(UploadedFile $file): string
    {
        if (! $file->isValid()) {
            throw new InvalidArgumentException($file->getErrorString() ?: 'File unduhan tidak valid.');
        }

        $mime = $file->getMimeType();

        if ($mime === null || ! in_array($mime, self::ALLOWED_MIMES, true)) {
            throw new InvalidArgumentException('Format file tidak didukung. Gunakan PDF, Word, Excel, JPEG, atau PNG.');
        }

        $targetDir = WRITEPATH . self::UPLOAD_SUBDIR;

        if (! is_dir($targetDir) && ! mkdir($targetDir, 0755, true) && ! is_dir($targetDir)) {
            throw new RuntimeException('Direktori unggahan unduhan tidak dapat dibuat.');
        }

        $storedName = $file->getRandomName();

        if (! $file->move($targetDir, $storedName)) {
            throw new RuntimeException('Gagal mengunggah unduhan.');
        }

        return self::UPLOAD_SUBDIR . '/' . $storedName;
    }

    public function fullPathFromRelative(string $relativePath): string
    {
        return WRITEPATH . ltrim($relativePath, '/');
    }

    public function publicDownloadUrl(int $id): string
    {
        return site_url(route_to('dokumen.download', $id));
    }

    private function removeStoredFile(string $relativePath): void
    {
        if ($relativePath === '') {
            return;
        }

        $fullPath = $this->fullPathFromRelative($relativePath);

        if (is_file($fullPath)) {
            unlink($fullPath);
        }
    }
}
