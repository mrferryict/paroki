<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Artikel\ArtikelKategoriDto;
use App\Entities\ArtikelKategoriRecord;
use App\Libraries\SlugGenerator;
use App\Models\ArtikelKategoriModel;
use App\Models\ArtikelModel;
use DomainException;
use RuntimeException;

class ArtikelKategoriService
{
    public function __construct(
        private readonly ArtikelKategoriModel $artikelKategoriModel,
        private readonly ArtikelModel $artikelModel,
        private readonly SlugGenerator $slugGenerator,
    ) {}

    /**
     * @return list<ArtikelKategoriRecord>
     */
    public function findAllActive(): array
    {
        /** @var list<ArtikelKategoriRecord> */
        return $this->artikelKategoriModel
            ->where('is_active', 1)
            ->orderBy('urutan', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
    }

    /**
     * @return list<ArtikelKategoriRecord>
     */
    public function findAllOrdered(): array
    {
        /** @var list<ArtikelKategoriRecord> */
        return $this->artikelKategoriModel
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
     * @return list<string>
     */
    public function activeSlugList(): array
    {
        return array_keys($this->kategoriOptions());
    }

    public function findById(int $id): ArtikelKategoriRecord
    {
        $record = $this->artikelKategoriModel->find($id);

        if ($record === null) {
            throw new DomainException('Kategori katekese tidak ditemukan.');
        }

        return $record;
    }

    public function getLabelForSlug(string $slug): ?string
    {
        $record = $this->artikelKategoriModel
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

        return $this->artikelKategoriModel
            ->where('slug', $slug)
            ->where('is_active', 1)
            ->countAllResults() > 0;
    }

    public function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $builder = $this->artikelKategoriModel->where('slug', $slug);

        if ($excludeId !== null) {
            $builder->where('id !=', $excludeId);
        }

        return $builder->countAllResults() > 0;
    }

    public function create(ArtikelKategoriDto $dto): int
    {
        $id = $this->artikelKategoriModel->insert($dto->toModelData());

        if ($id === false) {
            throw new RuntimeException('Gagal menyimpan kategori katekese.');
        }

        return (int) $id;
    }

    public function update(int $id, ArtikelKategoriDto $dto): void
    {
        $existing = $this->findById($id);
        $oldSlug  = (string) ($existing->slug ?? '');

        if ($oldSlug !== $dto->slug && $this->hasArtikelUsingSlug($oldSlug)) {
            throw new DomainException('Slug kategori tidak dapat diubah karena masih dipakai artikel.');
        }

        if (! $this->artikelKategoriModel->update($id, $dto->toModelData())) {
            throw new RuntimeException('Gagal memperbarui kategori katekese.');
        }

        if ($oldSlug !== $dto->slug) {
            $this->artikelModel
                ->where('kategori', $oldSlug)
                ->set(['kategori' => $dto->slug])
                ->update();
        }
    }

    public function delete(int $id): void
    {
        $existing = $this->findById($id);
        $slug     = (string) ($existing->slug ?? '');

        if ($this->hasArtikelUsingSlug($slug)) {
            throw new DomainException('Kategori tidak dapat dihapus karena masih dipakai artikel.');
        }

        if (! $this->artikelKategoriModel->delete($id)) {
            throw new RuntimeException('Gagal menghapus kategori katekese.');
        }
    }

    public function hasArtikelUsingSlug(string $slug): bool
    {
        return $this->countArtikelByKategori($slug) > 0;
    }

    public function buildDto(string $label, bool $isActive, int $urutan = 0, ?int $excludeId = null): ArtikelKategoriDto
    {
        return new ArtikelKategoriDto(
            slug: $this->generateUniqueSlug($label, $excludeId),
            label: $label,
            urutan: $urutan,
            isActive: $isActive,
        );
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

        $row = $this->artikelKategoriModel->selectMax('urutan')->first();

        if ($row === null) {
            return 1;
        }

        return ((int) ($row->urutan ?? 0)) + 1;
    }

    private function countArtikelByKategori(string $slug): int
    {
        if ($slug === '') {
            return 0;
        }

        return $this->artikelModel->where('kategori', $slug)->countAllResults();
    }
}
