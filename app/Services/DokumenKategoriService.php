<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Dokumen\DokumenKategoriDto;
use App\Entities\DokumenKategoriRecord;
use App\Libraries\SlugGenerator;
use App\Models\DokumenKategoriModel;
use App\Models\DokumenModel;
use DomainException;
use RuntimeException;

class DokumenKategoriService
{
    public function __construct(
        private readonly DokumenKategoriModel $dokumenKategoriModel,
        private readonly DokumenModel $dokumenModel,
        private readonly SlugGenerator $slugGenerator,
    ) {}

    /**
     * @return list<DokumenKategoriRecord>
     */
    public function findAllActive(): array
    {
        /** @var list<DokumenKategoriRecord> */
        return $this->dokumenKategoriModel
            ->where('is_active', 1)
            ->orderBy('urutan', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
    }

    /**
     * @return list<DokumenKategoriRecord>
     */
    public function findAllOrdered(): array
    {
        /** @var list<DokumenKategoriRecord> */
        return $this->dokumenKategoriModel
            ->orderBy('urutan', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
    }

    /**
     * @return array<string, string>
     */
    public function kategoriOptions(): array
    {
        $options = [];

        foreach ($this->findAllActive() as $record) {
            $options[(string) $record->slug] = (string) ($record->label ?? '');
        }

        return $options;
    }

    /**
     * @return array<string, string>
     */
    public function allKategoriLabels(): array
    {
        $options = [];

        foreach ($this->findAllOrdered() as $record) {
            $options[(string) $record->slug] = (string) ($record->label ?? '');
        }

        return $options;
    }

    /**
     * @return array<string, string>
     */
    public function kategoriOptionsForAdmin(?string $currentSlug = null): array
    {
        $options = $this->kategoriOptions();

        if ($currentSlug !== null && $currentSlug !== '' && ! array_key_exists($currentSlug, $options)) {
            $record = $this->dokumenKategoriModel->where('slug', $currentSlug)->first();

            if ($record !== null) {
                $options[$currentSlug] = (string) ($record->label ?? $currentSlug);
            }
        }

        return $options;
    }

    /**
     * @return list<string>
     */
    public function activeSlugList(): array
    {
        return array_keys($this->kategoriOptions());
    }

    /**
     * @return list<string>
     */
    public function allSlugList(): array
    {
        return array_map(
            static fn (DokumenKategoriRecord $record): string => (string) $record->slug,
            $this->findAllOrdered(),
        );
    }

    public function findById(int $id): DokumenKategoriRecord
    {
        $record = $this->dokumenKategoriModel->find($id);

        if ($record === null) {
            throw new DomainException('Kategori unduhan tidak ditemukan.');
        }

        return $record;
    }

    public function getLabelForSlug(string $slug): ?string
    {
        $record = $this->dokumenKategoriModel
            ->where('slug', $slug)
            ->where('is_active', 1)
            ->first();

        if ($record === null) {
            return null;
        }

        return (string) ($record->label ?? '');
    }

    public function isActiveSlug(string $slug): bool
    {
        if ($slug === '') {
            return false;
        }

        return $this->dokumenKategoriModel
            ->where('slug', $slug)
            ->where('is_active', 1)
            ->countAllResults() > 0;
    }

    public function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $builder = $this->dokumenKategoriModel->where('slug', $slug);

        if ($excludeId !== null) {
            $builder->where('id !=', $excludeId);
        }

        return $builder->countAllResults() > 0;
    }

    public function create(DokumenKategoriDto $dto): int
    {
        $id = $this->dokumenKategoriModel->insert($dto->toModelData());

        if ($id === false) {
            throw new RuntimeException('Gagal menyimpan kategori unduhan.');
        }

        return (int) $id;
    }

    public function update(int $id, DokumenKategoriDto $dto): void
    {
        $existing = $this->findById($id);
        $oldSlug  = (string) ($existing->slug ?? '');

        if ($oldSlug !== $dto->slug && $this->hasDokumenUsingSlug($oldSlug)) {
            throw new DomainException('Slug kategori tidak dapat diubah karena masih dipakai unduhan.');
        }

        if (! $this->dokumenKategoriModel->update($id, $dto->toModelData())) {
            throw new RuntimeException('Gagal memperbarui kategori unduhan.');
        }

        if ($oldSlug !== $dto->slug) {
            $this->dokumenModel
                ->where('kategori', $oldSlug)
                ->set(['kategori' => $dto->slug])
                ->update();
        }
    }

    public function delete(int $id): void
    {
        $existing = $this->findById($id);
        $slug     = (string) ($existing->slug ?? '');

        if ($this->hasDokumenUsingSlug($slug)) {
            throw new DomainException('Kategori tidak dapat dihapus karena masih dipakai unduhan. Nonaktifkan saja.');
        }

        if (! $this->dokumenKategoriModel->delete($id)) {
            throw new RuntimeException('Gagal menghapus kategori unduhan.');
        }
    }

    public function hasDokumenUsingSlug(string $slug): bool
    {
        return $this->countDokumenByKategori($slug) > 0;
    }

    public function generateUniqueSlug(string $label, ?int $excludeId = null): string
    {
        return $this->slugGenerator->unique(
            $label,
            fn (string $slug, ?int $exclude): bool => $this->slugExists($slug, $exclude),
            $excludeId,
        );
    }

    public function resolveUrutan(int $requestedUrutan): int
    {
        if ($requestedUrutan > 0) {
            return $requestedUrutan;
        }

        $row = $this->dokumenKategoriModel->selectMax('urutan')->first();

        if ($row === null) {
            return 1;
        }

        return ((int) ($row->urutan ?? 0)) + 1;
    }

    private function countDokumenByKategori(string $slug): int
    {
        if ($slug === '') {
            return 0;
        }

        return $this->dokumenModel->where('kategori', $slug)->countAllResults();
    }
}
