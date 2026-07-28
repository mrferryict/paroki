<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\DewanParokiBidang\DewanParokiBidangAdminRowDto;
use App\DTOs\DewanParokiBidang\DewanParokiBidangDto;
use App\DTOs\DewanParokiBidang\DewanParokiPenjabatAdminRowDto;
use App\Entities\DewanParokiBidang;
use App\Entities\DewanParokiPenjabat;
use App\Libraries\PiiCipher;
use App\Models\DewanParokiBidangModel;
use App\Models\DewanParokiPenjabatModel;
use DomainException;
use RuntimeException;

class DewanParokiBidangService
{
    public function __construct(
        private readonly DewanParokiBidangModel $dewanParokiBidangModel,
        private readonly DewanParokiPenjabatModel $penjabatModel,
        private readonly PiiCipher $piiCipher,
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

    /**
     * @return list<DewanParokiBidangAdminRowDto>
     */
    public function findAllForAdminTable(): array
    {
        $bidangList = $this->findAllOrdered();

        if ($bidangList === []) {
            return [];
        }

        $bidangIds = array_map(static fn (DewanParokiBidang $item): int => (int) $item->id, $bidangList);

        /** @var list<DewanParokiPenjabat> $penjabatRows */
        $penjabatRows = $this->penjabatModel
            ->select('id, bidang_id, nama, whatsapp_cipher, urutan')
            ->whereIn('bidang_id', $bidangIds)
            ->orderBy('urutan', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();

        /** @var array<int, list<DewanParokiPenjabatAdminRowDto>> $grouped */
        $grouped = [];

        foreach ($penjabatRows as $penjabat) {
            $kontak = $this->piiCipher->decrypt((string) $penjabat->whatsapp_cipher);

            if ($kontak === null || $kontak === '') {
                throw new RuntimeException('Nomor WhatsApp penjabat tidak dapat didekripsi.');
            }

            $bidangId = (int) $penjabat->bidang_id;
            $grouped[$bidangId][] = new DewanParokiPenjabatAdminRowDto(
                id: (int) $penjabat->id,
                nama: (string) ($penjabat->nama ?? ''),
                whatsapp: $kontak,
            );
        }

        $kodeOptions = $this->kodeOptions();
        $rows        = [];

        foreach ($bidangList as $bidang) {
            $bidangId = (int) $bidang->id;

            $rows[] = new DewanParokiBidangAdminRowDto(
                id: $bidangId,
                kode: (string) ($bidang->kode ?? ''),
                kodeLabel: $kodeOptions[(string) ($bidang->kode ?? '')] ?? (string) ($bidang->kode ?? ''),
                nama: (string) ($bidang->nama_tampilan ?? ''),
                deskripsi: $bidang->deskripsi !== null ? (string) $bidang->deskripsi : null,
                icon: (string) ($bidang->icon ?? ''),
                urutan: (int) ($bidang->urutan ?? 0),
                penjabat: $grouped[$bidangId] ?? [],
            );
        }

        return $rows;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function findAllForPublicWithPenjabat(): array
    {
        $bidangList = $this->findAllOrdered();

        if ($bidangList === []) {
            return [];
        }

        $bidangIds = array_map(static fn (DewanParokiBidang $item): int => (int) $item->id, $bidangList);

        /** @var list<DewanParokiPenjabat> $penjabatRows */
        $penjabatRows = $this->penjabatModel
            ->select('id, bidang_id, nama, urutan')
            ->whereIn('bidang_id', $bidangIds)
            ->orderBy('urutan', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();

        /** @var array<int, list<array{id: int, nama: string}>> $grouped */
        $grouped = [];

        foreach ($penjabatRows as $penjabat) {
            $bidangId = (int) $penjabat->bidang_id;
            $grouped[$bidangId][] = [
                'id'   => (int) $penjabat->id,
                'nama' => (string) ($penjabat->nama ?? ''),
            ];
        }

        return array_map(static function (DewanParokiBidang $bidang) use ($grouped): array {
            $bidangId = (int) $bidang->id;

            return [
                'kode'      => (string) ($bidang->kode ?? ''),
                'nama'      => (string) ($bidang->nama_tampilan ?? ''),
                'deskripsi' => (string) ($bidang->deskripsi ?? ''),
                'icon'      => (string) ($bidang->icon ?? ''),
                'penjabat'  => $grouped[$bidangId] ?? [],
            ];
        }, $bidangList);
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
