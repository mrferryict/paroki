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
    ) {}

    /**
     * @return list<Dokumen>
     */
    public function findAllOrdered(): array
    {
        /** @var list<Dokumen> */
        return $this->dokumenModel
            ->select('id, nama, kategori, created_at, updated_at')
            ->orderBy('kategori', 'ASC')
            ->orderBy('nama', 'ASC')
            ->findAll();
    }

    public function findById(int $id): Dokumen
    {
        $item = $this->dokumenModel->find($id);

        if ($item === null) {
            throw new DomainException('Dokumen tidak ditemukan.');
        }

        return $item;
    }

    public function create(DokumenDto $dto): int
    {
        $id = $this->dokumenModel->insert($dto->toModelData());

        if ($id === false) {
            throw new RuntimeException('Gagal menyimpan dokumen.');
        }

        return (int) $id;
    }

    public function update(int $id, DokumenDto $dto): void
    {
        $existing = $this->findById($id);
        $oldPath  = (string) $existing->file_path;

        if (! $this->dokumenModel->update($id, $dto->toModelData())) {
            throw new RuntimeException('Gagal memperbarui dokumen.');
        }

        if ($oldPath !== '' && $oldPath !== $dto->filePath) {
            $this->removeStoredFile($oldPath);
        }
    }

    public function delete(int $id): void
    {
        $existing = $this->findById($id);

        if (! $this->dokumenModel->delete($id)) {
            throw new RuntimeException('Gagal menghapus dokumen.');
        }

        $this->removeStoredFile((string) $existing->file_path);
    }

    public function resolveDownload(int $id): DokumenDownloadDto
    {
        $dokumen = $this->findById($id);
        $fullPath = $this->fullPathFromRelative((string) $dokumen->file_path);

        if (! is_file($fullPath)) {
            throw new DomainException('Berkas dokumen tidak ditemukan di server.');
        }

        $extension = pathinfo($fullPath, PATHINFO_EXTENSION);
        $clientName = (string) $dokumen->nama;

        if ($extension !== '' && ! str_ends_with(strtolower($clientName), '.' . strtolower($extension))) {
            $clientName .= '.' . $extension;
        }

        return new DokumenDownloadDto(
            fullPath: $fullPath,
            clientName: $clientName,
        );
    }

    public function storeUploadedFile(UploadedFile $file): string
    {
        if (! $file->isValid()) {
            throw new InvalidArgumentException($file->getErrorString() ?: 'File dokumen tidak valid.');
        }

        $mime = $file->getMimeType();

        if ($mime === null || ! in_array($mime, self::ALLOWED_MIMES, true)) {
            throw new InvalidArgumentException('Format file tidak didukung. Gunakan PDF, Word, Excel, JPEG, atau PNG.');
        }

        $targetDir = WRITEPATH . self::UPLOAD_SUBDIR;

        if (! is_dir($targetDir) && ! mkdir($targetDir, 0755, true) && ! is_dir($targetDir)) {
            throw new RuntimeException('Direktori unggahan dokumen tidak dapat dibuat.');
        }

        $storedName = $file->getRandomName();

        if (! $file->move($targetDir, $storedName)) {
            throw new RuntimeException('Gagal mengunggah dokumen.');
        }

        return self::UPLOAD_SUBDIR . '/' . $storedName;
    }

    public function fullPathFromRelative(string $relativePath): string
    {
        return WRITEPATH . ltrim($relativePath, '/');
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
