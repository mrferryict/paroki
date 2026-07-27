<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\JadwalMisa\JadwalMisaDto;
use App\Entities\JadwalMisa;
use App\Models\JadwalMisaModel;
use DomainException;
use RuntimeException;

class JadwalMisaService
{
    public function __construct(
        private readonly JadwalMisaModel $jadwalMisaModel,
    ) {}

    /**
     * @return list<JadwalMisa>
     */
    public function findAllOrdered(): array
    {
        /** @var list<JadwalMisa> */
        return $this->jadwalMisaModel
            ->orderBy('urutan', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
    }

    public function findById(int $id): JadwalMisa
    {
        $jadwal = $this->jadwalMisaModel->find($id);

        if ($jadwal === null) {
            throw new DomainException('Jadwal misa tidak ditemukan.');
        }

        return $jadwal;
    }

    public function create(JadwalMisaDto $dto): int
    {
        $data              = $dto->toModelData();
        $data['urutan']    = $this->resolveUrutan($dto->urutan);
        $data['is_active'] = $dto->isActive ? 1 : 0;
        $data['jam']       = $this->normalizeJam($dto->jam);

        $id = $this->jadwalMisaModel->insert($data);

        if ($id === false) {
            throw new RuntimeException('Gagal menyimpan jadwal misa.');
        }

        return (int) $id;
    }

    public function update(int $id, JadwalMisaDto $dto): void
    {
        $existing = $this->findById($id);

        $data              = $dto->toModelData();
        $data['urutan']    = $dto->urutan > 0 ? $dto->urutan : (int) $existing->urutan;
        $data['is_active'] = $dto->isActive ? 1 : 0;
        $data['jam']       = $this->normalizeJam($dto->jam);

        if (! $this->jadwalMisaModel->update($id, $data)) {
            throw new RuntimeException('Gagal memperbarui jadwal misa.');
        }
    }

    public function delete(int $id): void
    {
        $this->findById($id);

        if (! $this->jadwalMisaModel->delete($id)) {
            throw new RuntimeException('Gagal menghapus jadwal misa.');
        }
    }

    public function moveUp(int $id): void
    {
        $current  = $this->findById($id);
        $neighbor = $this->jadwalMisaModel
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
        $neighbor = $this->jadwalMisaModel
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

        $row = $this->jadwalMisaModel->selectMax('urutan')->first();

        if ($row === null) {
            return 1;
        }

        return ((int) ($row->urutan ?? 0)) + 1;
    }

    /**
     * @return array<string, string>
     */
    public function jenisOptions(): array
    {
        return [
            'harian'        => 'Harian',
            'mingguan'      => 'Mingguan',
            'jumat_pertama' => 'Jumat Pertama',
            'khusus'        => 'Khusus',
        ];
    }

    public function formatJamForInput(string $jam): string
    {
        return substr($this->normalizeJam($jam), 0, 5);
    }

    private function normalizeJam(string $jam): string
    {
        $jam = trim($jam);

        if (preg_match('/^\d{2}:\d{2}$/', $jam) === 1) {
            return $jam . ':00';
        }

        if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $jam) === 1) {
            return $jam;
        }

        throw new DomainException('Format jam tidak valid.');
    }

    private function swapUrutan(JadwalMisa $a, JadwalMisa $b): void
    {
        $db = db_connect();
        $db->transStart();

        $this->jadwalMisaModel->update((int) $a->id, ['urutan' => (int) $b->urutan]);
        $this->jadwalMisaModel->update((int) $b->id, ['urutan' => (int) $a->urutan]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new RuntimeException('Gagal mengubah urutan jadwal misa.');
        }
    }
}
