<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\HeroSlide\HeroSlideDto;
use App\Entities\HeroSlide;
use App\Models\HeroSlideModel;
use CodeIgniter\HTTP\Files\UploadedFile;
use DomainException;
use InvalidArgumentException;
use RuntimeException;

class HeroSlideService
{
    private const UPLOAD_SUBDIR = 'uploads/hero';

    /** @var list<string> */
    private const ALLOWED_MIMES = [
        'image/jpeg',
        'image/png',
        'image/webp',
    ];

    public function __construct(
        private readonly HeroSlideModel $heroSlideModel,
    ) {}

    /**
     * @return list<HeroSlide>
     */
    public function findAllOrdered(): array
    {
        /** @var list<HeroSlide> */
        return $this->heroSlideModel
            ->orderBy('urutan', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
    }

    public function findById(int $id): HeroSlide
    {
        $slide = $this->heroSlideModel->find($id);

        if ($slide === null) {
            throw new DomainException('Hero slide tidak ditemukan.');
        }

        return $slide;
    }

    public function create(HeroSlideDto $dto): int
    {
        $data               = $dto->toModelData();
        $data['urutan']     = $this->resolveUrutan($dto->urutan);
        $data['is_active']  = $dto->isActive ? 1 : 0;

        $id = $this->heroSlideModel->insert($data);

        if ($id === false) {
            throw new RuntimeException('Gagal menyimpan hero slide.');
        }

        return (int) $id;
    }

    public function update(int $id, HeroSlideDto $dto): void
    {
        $existing = $this->findById($id);

        $data              = $dto->toModelData();
        $data['urutan']    = $dto->urutan > 0 ? $dto->urutan : (int) $existing->urutan;
        $data['is_active'] = $dto->isActive ? 1 : 0;

        if (! $this->heroSlideModel->update($id, $data)) {
            throw new RuntimeException('Gagal memperbarui hero slide.');
        }
    }

    public function delete(int $id): void
    {
        $existing = $this->findById($id);

        if (! $this->heroSlideModel->delete($id)) {
            throw new RuntimeException('Gagal menghapus hero slide.');
        }

        $this->removeImageIfUnused((string) $existing->gambar);
    }

    public function moveUp(int $id): void
    {
        $current = $this->findById($id);
        $neighbor = $this->heroSlideModel
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
        $current = $this->findById($id);
        $neighbor = $this->heroSlideModel
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

        if ($mime === null || ! in_array($mime, self::ALLOWED_MIMES, true)) {
            throw new InvalidArgumentException('Format gambar harus JPEG, PNG, atau WebP.');
        }

        $targetDir = FCPATH . self::UPLOAD_SUBDIR;

        if (! is_dir($targetDir) && ! mkdir($targetDir, 0755, true) && ! is_dir($targetDir)) {
            throw new RuntimeException('Direktori unggahan hero slide tidak dapat dibuat.');
        }

        $storedName = $file->getRandomName();

        if (! $file->move($targetDir, $storedName)) {
            throw new RuntimeException('Gagal mengunggah gambar hero slide.');
        }

        return self::UPLOAD_SUBDIR . '/' . $storedName;
    }

    public function resolveUrutan(int $requestedUrutan): int
    {
        if ($requestedUrutan > 0) {
            return $requestedUrutan;
        }

        $row = $this->heroSlideModel->selectMax('urutan')->first();

        if ($row === null) {
            return 1;
        }

        return ((int) ($row->urutan ?? 0)) + 1;
    }

    private function swapUrutan(HeroSlide $a, HeroSlide $b): void
    {
        $db = db_connect();
        $db->transStart();

        $this->heroSlideModel->update((int) $a->id, ['urutan' => (int) $b->urutan]);
        $this->heroSlideModel->update((int) $b->id, ['urutan' => (int) $a->urutan]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new RuntimeException('Gagal mengubah urutan hero slide.');
        }
    }

    private function removeImageIfUnused(string $relativePath): void
    {
        if ($relativePath === '') {
            return;
        }

        $inUse = $this->heroSlideModel
            ->where('gambar', $relativePath)
            ->countAllResults();

        if ($inUse > 0) {
            return;
        }

        $fullPath = FCPATH . ltrim($relativePath, '/');

        if (is_file($fullPath)) {
            unlink($fullPath);
        }
    }
}
