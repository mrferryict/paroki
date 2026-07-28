<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\SakramenJenis\SakramenJenisDto;
use App\Entities\SakramenJenis;
use App\Enums\LayananGrup;
use App\Enums\SakramenJenisKode;
use App\Models\SakramenJenisModel;
use DomainException;
use InvalidArgumentException;
use RuntimeException;

class SakramenJenisService
{
    public function __construct(
        private readonly SakramenJenisModel $sakramenJenisModel,
    ) {}

    /**
     * @return list<SakramenJenis>
     */
    public function findAllOrdered(): array
    {
        /** @var list<SakramenJenis> */
        return $this->sakramenJenisModel
            ->orderBy('urutan', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
    }

    /**
     * @return list<SakramenJenis>
     */
    public function findAllActiveOrdered(): array
    {
        /** @var list<SakramenJenis> */
        return $this->sakramenJenisModel
            ->where('is_active', 1)
            ->orderBy('urutan', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
    }

    public function findById(int $id): SakramenJenis
    {
        $jenis = $this->sakramenJenisModel->find($id);

        if ($jenis === null) {
            throw new DomainException('Jenis layanan tidak ditemukan.');
        }

        return $jenis;
    }

    public function create(SakramenJenisDto $dto): int
    {
        if ($this->sakramenJenisModel->where('kode', $dto->kode)->first() !== null) {
            throw new DomainException('Kode jenis layanan sudah digunakan.');
        }

        $data              = $dto->toModelData();
        $data['urutan']    = $this->resolveUrutan($dto->urutan);
        $data['is_active'] = $dto->isActive ? 1 : 0;

        $id = $this->sakramenJenisModel->insert($data);

        if ($id === false) {
            throw new RuntimeException('Gagal menyimpan jenis layanan.');
        }

        return (int) $id;
    }

    public function update(int $id, SakramenJenisDto $dto): void
    {
        $existing = $this->findById($id);

        $data              = $dto->toModelData();
        $data['kode']      = (string) $existing->kode;
        $data['grup']      = (string) ($existing->grup ?? $dto->grup);
        $data['urutan']    = $dto->urutan > 0 ? $dto->urutan : (int) $existing->urutan;
        $data['is_active'] = $dto->isActive ? 1 : 0;

        if (! $this->sakramenJenisModel->update($id, $data)) {
            throw new RuntimeException('Gagal memperbarui jenis layanan.');
        }
    }

    public function delete(int $id): void
    {
        $this->findById($id);

        if (! $this->sakramenJenisModel->delete($id)) {
            throw new RuntimeException('Gagal menghapus jenis layanan.');
        }
    }

    public function moveUp(int $id): void
    {
        $current  = $this->findById($id);
        $neighbor = $this->sakramenJenisModel
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
        $neighbor = $this->sakramenJenisModel
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

        $row = $this->sakramenJenisModel->selectMax('urutan')->first();

        if ($row === null) {
            return 1;
        }

        return ((int) ($row->urutan ?? 0)) + 1;
    }

    public function resolveGrupForKode(string $kode): string
    {
        $enum = SakramenJenisKode::tryFromString($kode);

        if ($enum === null) {
            throw new InvalidArgumentException('Kode jenis layanan tidak valid.');
        }

        return $enum->grup()->value;
    }

    /**
     * @return array<string, string>
     */
    public function kodeOptions(): array
    {
        return SakramenJenisKode::options();
    }

    /**
     * @return array<string, string>
     */
    public function grupOptions(): array
    {
        return LayananGrup::options();
    }

    private function swapUrutan(SakramenJenis $a, SakramenJenis $b): void
    {
        $db = db_connect();
        $db->transStart();

        $this->sakramenJenisModel->update((int) $a->id, ['urutan' => (int) $b->urutan]);
        $this->sakramenJenisModel->update((int) $b->id, ['urutan' => (int) $a->urutan]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new RuntimeException('Gagal mengubah urutan jenis layanan.');
        }
    }
}
