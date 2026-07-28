<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Galeri\GaleriEventDto;
use App\Entities\GaleriEvent;
use App\Libraries\SlugGenerator;
use App\Models\GaleriEventModel;
use DomainException;
use RuntimeException;

class GaleriEventService
{
    public function __construct(
        private readonly GaleriEventModel $galeriEventModel,
        private readonly SlugGenerator $slugGenerator,
    ) {}

    /**
     * @return list<GaleriEvent>
     */
    public function findAllOrdered(): array
    {
        /** @var list<GaleriEvent> */
        return $this->galeriEventModel
            ->orderBy('urutan', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
    }

    public function findById(int $id): GaleriEvent
    {
        $event = $this->galeriEventModel->find($id);

        if ($event === null) {
            throw new DomainException('Event galeri tidak ditemukan.');
        }

        return $event;
    }

    public function create(GaleriEventDto $dto): int
    {
        $data           = $dto->toModelData();
        $data['urutan'] = $this->resolveUrutan($dto->urutan);

        $id = $this->galeriEventModel->insert($data);

        if ($id === false) {
            throw new RuntimeException('Gagal menyimpan event galeri.');
        }

        return (int) $id;
    }

    public function update(int $id, GaleriEventDto $dto): void
    {
        $existing = $this->findById($id);

        $data           = $dto->toModelData();
        $data['urutan'] = $dto->urutan > 0 ? $dto->urutan : (int) $existing->urutan;

        if (! $this->galeriEventModel->update($id, $data)) {
            throw new RuntimeException('Gagal memperbarui event galeri.');
        }
    }

    public function delete(int $id): void
    {
        $this->findById($id);

        if (! $this->galeriEventModel->delete($id)) {
            throw new RuntimeException('Gagal menghapus event galeri.');
        }
    }

    public function moveUp(int $id): void
    {
        $current  = $this->findById($id);
        $neighbor = $this->galeriEventModel
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
        $neighbor = $this->galeriEventModel
            ->where('urutan >', (int) $current->urutan)
            ->orderBy('urutan', 'ASC')
            ->first();

        if ($neighbor === null) {
            return;
        }

        $this->swapUrutan($current, $neighbor);
    }

    public function buildDto(string $judul, ?int $excludeId = null, int $urutan = 0): GaleriEventDto
    {
        return new GaleriEventDto(
            judul: $judul,
            slug: $this->generateUniqueSlug($judul, $excludeId),
            urutan: $urutan,
        );
    }

    public function generateUniqueSlug(string $judul, ?int $excludeId = null): string
    {
        return $this->slugGenerator->unique(
            $judul,
            fn (string $slug, ?int $exclude): bool => $this->slugExists($slug, $exclude),
            $excludeId,
        );
    }

    public function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $builder = $this->galeriEventModel->where('slug', $slug);

        if ($excludeId !== null) {
            $builder->where('id !=', $excludeId);
        }

        return $builder->countAllResults() > 0;
    }

    public function resolveUrutan(int $requestedUrutan): int
    {
        if ($requestedUrutan > 0) {
            return $requestedUrutan;
        }

        $row = $this->galeriEventModel->selectMax('urutan')->first();

        if ($row === null) {
            return 1;
        }

        return ((int) ($row->urutan ?? 0)) + 1;
    }

    public function incrementViewCount(int $id): void
    {
        $this->galeriEventModel
            ->where('id', $id)
            ->set('view_count', 'view_count + 1', false)
            ->update();
    }

    private function swapUrutan(GaleriEvent $a, GaleriEvent $b): void
    {
        $db = db_connect();
        $db->transStart();

        $this->galeriEventModel->update((int) $a->id, ['urutan' => (int) $b->urutan]);
        $this->galeriEventModel->update((int) $b->id, ['urutan' => (int) $a->urutan]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new RuntimeException('Gagal mengubah urutan event galeri.');
        }
    }
}
