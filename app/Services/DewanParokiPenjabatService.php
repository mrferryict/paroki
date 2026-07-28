<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\DewanParokiBidang\DewanParokiPenjabatDto;
use App\Entities\DewanParokiPenjabat;
use App\Libraries\PiiCipher;
use App\Models\DewanParokiBidangModel;
use App\Models\DewanParokiPenjabatModel;
use DomainException;
use InvalidArgumentException;
use RuntimeException;

class DewanParokiPenjabatService
{
    public function __construct(
        private readonly DewanParokiPenjabatModel $penjabatModel,
        private readonly DewanParokiBidangModel $bidangModel,
        private readonly PiiCipher $piiCipher,
    ) {}

    /**
     * @return list<DewanParokiPenjabat>
     */
    public function findAllByBidangIdForAdmin(int $bidangId): array
    {
        $this->assertBidangExists($bidangId);

        /** @var list<DewanParokiPenjabat> */
        return $this->penjabatModel
            ->select('id, bidang_id, nama, whatsapp_cipher, whatsapp_hash, urutan, created_at, updated_at')
            ->where('bidang_id', $bidangId)
            ->orderBy('urutan', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
    }

    /**
     * @return list<DewanParokiPenjabat>
     */
    public function findAllByBidangIdsForPublic(array $bidangIds): array
    {
        if ($bidangIds === []) {
            return [];
        }

        /** @var list<DewanParokiPenjabat> */
        return $this->penjabatModel
            ->select('id, bidang_id, nama, urutan')
            ->whereIn('bidang_id', $bidangIds)
            ->orderBy('urutan', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
    }

    public function decryptWhatsapp(DewanParokiPenjabat $penjabat): string
    {
        $kontak = $this->piiCipher->decrypt((string) $penjabat->whatsapp_cipher);

        if ($kontak === null || $kontak === '') {
            throw new RuntimeException('Nomor WhatsApp penjabat tidak dapat didekripsi.');
        }

        return $kontak;
    }

    public function create(DewanParokiPenjabatDto $dto): int
    {
        $this->assertBidangExists($dto->bidangId);
        $whatsappFields = $this->encryptWhatsapp($dto->whatsapp);

        $id = $this->penjabatModel->insert([
            'bidang_id'        => $dto->bidangId,
            'nama'             => $dto->nama,
            'whatsapp_cipher'  => $whatsappFields['cipher'],
            'whatsapp_hash'    => $whatsappFields['hash'],
            'urutan'           => $this->resolveUrutan(bidangId: $dto->bidangId),
        ]);

        if ($id === false) {
            throw new RuntimeException('Gagal menyimpan penjabat DPH.');
        }

        return (int) $id;
    }

    public function update(int $id, DewanParokiPenjabatDto $dto): void
    {
        $existing = $this->findById($id);

        if ((int) $existing->bidang_id !== $dto->bidangId) {
            throw new DomainException('Penjabat tidak ditemukan pada bidang ini.');
        }

        $whatsappFields = $this->resolveWhatsappForUpdate(
            existingCipher: (string) $existing->whatsapp_cipher,
            existingHash: (string) $existing->whatsapp_hash,
            newWhatsapp: $dto->whatsapp,
        );

        if (! $this->penjabatModel->update($id, [
            'nama'            => $dto->nama,
            'whatsapp_cipher' => $whatsappFields['cipher'],
            'whatsapp_hash'   => $whatsappFields['hash'],
        ])) {
            throw new RuntimeException('Gagal memperbarui penjabat DPH.');
        }
    }

    public function delete(int $id, int $bidangId): void
    {
        $existing = $this->findById($id);

        if ((int) $existing->bidang_id !== $bidangId) {
            throw new DomainException('Penjabat tidak ditemukan pada bidang ini.');
        }

        if (! $this->penjabatModel->delete($id)) {
            throw new RuntimeException('Gagal menghapus penjabat DPH.');
        }
    }

    public function findById(int $id): DewanParokiPenjabat
    {
        $item = $this->penjabatModel->find($id);

        if ($item === null) {
            throw new DomainException('Penjabat DPH tidak ditemukan.');
        }

        return $item;
    }

    private function resolveUrutan(int $bidangId): int
    {
        $row = $this->penjabatModel
            ->selectMax('urutan')
            ->where('bidang_id', $bidangId)
            ->first();

        if ($row === null) {
            return 1;
        }

        return ((int) ($row->urutan ?? 0)) + 1;
    }

    /**
     * @return array{cipher: string, hash: string}
     */
    private function encryptWhatsapp(string $whatsapp): array
    {
        $whatsapp = trim($whatsapp);

        if ($whatsapp === '') {
            throw new InvalidArgumentException('Nomor WhatsApp wajib diisi.');
        }

        return [
            'cipher' => $this->piiCipher->encrypt($whatsapp),
            'hash'   => $this->piiCipher->hashPhone($whatsapp),
        ];
    }

    /**
     * @return array{cipher: string, hash: string}
     */
    private function resolveWhatsappForUpdate(string $existingCipher, string $existingHash, string $newWhatsapp): array
    {
        $newWhatsapp = trim($newWhatsapp);

        if ($newWhatsapp === '') {
            return [
                'cipher' => $existingCipher,
                'hash'   => $existingHash,
            ];
        }

        return $this->encryptWhatsapp($newWhatsapp);
    }

    private function assertBidangExists(int $bidangId): void
    {
        if ($this->bidangModel->find($bidangId) === null) {
            throw new DomainException('Bidang DPH tidak ditemukan.');
        }
    }
}
