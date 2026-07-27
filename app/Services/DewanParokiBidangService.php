<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\DewanParokiBidang\DewanParokiBidangDto;
use App\Entities\DewanParokiBidang;
use App\Models\DewanParokiBidangModel;
use DomainException;
use RuntimeException;

class DewanParokiBidangService
{
    public function __construct(
        private readonly DewanParokiBidangModel $dewanParokiBidangModel,
    ) {}

    /**
     * @return list<DewanParokiBidang>
     */
    public function findAllOrdered(): array
    {
        /** @var list<DewanParokiBidang> */
        return $this->dewanParokiBidangModel
            ->orderBy('urutan', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
    }

    public function findById(int $id): DewanParokiBidang
    {
        $bidang = $this->dewanParokiBidangModel->find($id);

        if ($bidang === null) {
            throw new DomainException('Bidang DPH tidak ditemukan.');
        }

        return $bidang;
    }

    public function create(DewanParokiBidangDto $dto): int
    {
        if ($this->dewanParokiBidangModel->where('kode', $dto->kode)->first() !== null) {
            throw new DomainException('Kode bidang sudah digunakan.');
        }

        $data           = $dto->toModelData();
        $data['urutan'] = $this->resolveUrutan($dto->urutan);

        $id = $this->dewanParokiBidangModel->insert($data);

        if ($id === false) {
            throw new RuntimeException('Gagal menyimpan bidang DPH.');
        }

        return (int) $id;
    }

    public function update(int $id, DewanParokiBidangDto $dto): void
    {
        $existing = $this->findById($id);

        $data           = $dto->toModelData();
        $data['kode']   = (string) $existing->kode;
        $data['urutan'] = $dto->urutan > 0 ? $dto->urutan : (int) $existing->urutan;

        if (! $this->dewanParokiBidangModel->update($id, $data)) {
            throw new RuntimeException('Gagal memperbarui bidang DPH.');
        }
    }

    public function delete(int $id): void
    {
        $this->findById($id);

        if (! $this->dewanParokiBidangModel->delete($id)) {
            throw new RuntimeException('Gagal menghapus bidang DPH.');
        }
    }

    public function moveUp(int $id): void
    {
        $current  = $this->findById($id);
        $neighbor = $this->dewanParokiBidangModel
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
        $neighbor = $this->dewanParokiBidangModel
            ->where('urutan >', (int) $current->urutan)
            ->orderBy('urutan', 'ASC')
            ->first();

        if ($neighbor === null) {
            return;
        }

        $this->swapUrutan($current, $neighbor);
    }

    public function resolveUrutan(int $requestedUrutan): int
    {
        if ($requestedUrutan > 0) {
            return $requestedUrutan;
        }

        $row = $this->dewanParokiBidangModel->selectMax('urutan')->first();

        if ($row === null) {
            return 1;
        }

        return ((int) ($row->urutan ?? 0)) + 1;
    }

    /**
     * @return array<string, string>
     */
    public function kodeOptions(): array
    {
        return [
            'liturgi'  => 'Liturgi',
            'diakonia' => 'Diakonia',
            'koinonia' => 'Koinonia',
            'kerygma'  => 'Kerygma',
        ];
    }

    private function swapUrutan(DewanParokiBidang $a, DewanParokiBidang $b): void
    {
        $db = db_connect();
        $db->transStart();

        $this->dewanParokiBidangModel->update((int) $a->id, ['urutan' => (int) $b->urutan]);
        $this->dewanParokiBidangModel->update((int) $b->id, ['urutan' => (int) $a->urutan]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new RuntimeException('Gagal mengubah urutan bidang DPH.');
        }
    }
}
