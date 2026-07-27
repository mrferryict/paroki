<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Galeri\GaleriDto;
use App\Entities\Galeri;
use App\Models\GaleriModel;
use CodeIgniter\HTTP\Files\UploadedFile;
use DomainException;
use InvalidArgumentException;
use RuntimeException;

class GaleriService
{
    private const UPLOAD_SUBDIR = 'uploads/galeri';

    /** @var list<string> */
    private const ALLOWED_IMAGE_MIMES = [
        'image/jpeg',
        'image/png',
        'image/webp',
    ];

    public function __construct(
        private readonly GaleriModel $galeriModel,
    ) {}

    /**
     * @return list<Galeri>
     */
    public function findAllOrdered(): array
    {
        /** @var list<Galeri> */
        return $this->galeriModel
            ->orderBy('urutan', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
    }

    public function findById(int $id): Galeri
    {
        $item = $this->galeriModel->find($id);

        if ($item === null) {
            throw new DomainException('Item galeri tidak ditemukan.');
        }

        return $item;
    }

    public function create(GaleriDto $dto): int
    {
        $data           = $dto->toModelData();
        $data['urutan'] = $this->resolveUrutan($dto->urutan);

        $id = $this->galeriModel->insert($data);

        if ($id === false) {
            throw new RuntimeException('Gagal menyimpan item galeri.');
        }

        return (int) $id;
    }

    public function update(int $id, GaleriDto $dto): void
    {
        $existing = $this->findById($id);

        $data           = $dto->toModelData();
        $data['urutan'] = $dto->urutan > 0 ? $dto->urutan : (int) $existing->urutan;

        if (! $this->galeriModel->update($id, $data)) {
            throw new RuntimeException('Gagal memperbarui item galeri.');
        }
    }

    public function delete(int $id): void
    {
        $existing = $this->findById($id);

        if (! $this->galeriModel->delete($id)) {
            throw new RuntimeException('Gagal menghapus item galeri.');
        }

        $this->removeFileIfUnused((string) $existing->file_path);
    }

    public function moveUp(int $id): void
    {
        $current  = $this->findById($id);
        $neighbor = $this->galeriModel
            ->where('urutan <', (int) $current->urutan)
            ->orderBy('urutan', 'DESC')
            ->first();

        if ($neighbor === null) {
            return;
        }

        $this->swapUrutan($current, $neighbor);
    }

    public function moveDown(int $id): void
    {
        $current  = $this->findById($id);
        $neighbor = $this->galeriModel
            ->where('urutan >', (int) $current->urutan)
            ->orderBy('urutan', 'ASC')
            ->first();

        if ($neighbor === null) {
            return;
        }

        $this->swapUrutan($current, $neighbor);
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
            throw new RuntimeException('Direktori unggahan galeri tidak dapat dibuat.');
        }

        $storedName = $file->getRandomName();

        if (! $file->move($targetDir, $storedName)) {
            throw new RuntimeException('Gagal mengunggah gambar galeri.');
        }

        return self::UPLOAD_SUBDIR . '/' . $storedName;
    }

    public function publicUrl(string $relativePath): string
    {
        return base_url(ltrim($relativePath, '/'));
    }

    public function resolveUrutan(int $requestedUrutan): int
    {
        if ($requestedUrutan > 0) {
            return $requestedUrutan;
        }

        $row = $this->galeriModel->selectMax('urutan')->first();

        if ($row === null) {
            return 1;
        }

        return ((int) ($row->urutan ?? 0)) + 1;
    }

    private function swapUrutan(Galeri $a, Galeri $b): void
    {
        $db = db_connect();
        $db->transStart();

        $this->galeriModel->update((int) $a->id, ['urutan' => (int) $b->urutan]);
        $this->galeriModel->update((int) $b->id, ['urutan' => (int) $a->urutan]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new RuntimeException('Gagal mengubah urutan galeri.');
        }
    }

    private function removeFileIfUnused(string $relativePath): void
    {
        if ($relativePath === '') {
            return;
        }

        if ($this->galeriModel->where('file_path', $relativePath)->countAllResults() > 0) {
            return;
        }

        $fullPath = FCPATH . ltrim($relativePath, '/');

        if (is_file($fullPath)) {
            unlink($fullPath);
        }
    }
}
