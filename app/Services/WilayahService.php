<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Wilayah\LingkunganAdminRowDto;
use App\DTOs\Wilayah\WilayahAdminRowDto;
use App\DTOs\Wilayah\WilayahDetailDto;
use App\DTOs\Wilayah\WilayahDto;
use App\DTOs\Wilayah\WilayahWithLingkunganDto;
use App\Entities\Lingkungan;
use App\Entities\Wilayah;
use App\Libraries\PiiCipher;
use App\Repositories\WilayahRepository;
use DomainException;
use InvalidArgumentException;
use RuntimeException;

class WilayahService
{
    public function __construct(
        private readonly WilayahRepository $wilayahRepository,
        private readonly PiiCipher $piiCipher,
    ) {}

    /**
     * @return list<Wilayah>
     */
    public function findAllForList(): array
    {
        return $this->wilayahRepository->findAllForList();
    }

    /**
     * @return list<WilayahAdminRowDto>
     */
    public function findAllForAdminTable(): array
    {
        $wilayahList = $this->wilayahRepository->findAllForAdminMaster();

        if ($wilayahList === []) {
            return [];
        }

        $wilayahIds = array_map(static fn (Wilayah $wilayah): int => (int) $wilayah->id, $wilayahList);
        $lingkungan = $this->wilayahRepository->findLingkunganForAdminByWilayahIds($wilayahIds);

        /** @var array<int, list<LingkunganAdminRowDto>> $lingkunganByWilayah */
        $lingkunganByWilayah = [];

        foreach ($lingkungan as $item) {
            $kontak = null;

            if ($item->ketua_kontak_cipher !== null && $item->ketua_kontak_cipher !== '') {
                $kontak = $this->piiCipher->decrypt((string) $item->ketua_kontak_cipher);
            }

            $wilayahId = (int) $item->wilayah_id;
            $lingkunganByWilayah[$wilayahId][] = new LingkunganAdminRowDto(
                id: (int) $item->id,
                wilayahId: $wilayahId,
                nama: (string) ($item->nama ?? ''),
                ketuaNama: (string) ($item->ketua_nama ?? ''),
                ketuaKontak: $kontak !== null && $kontak !== '' ? $kontak : null,
            );
        }

        $rows = [];

        foreach ($wilayahList as $wilayah) {
            $kontak = $this->piiCipher->decrypt((string) $wilayah->ketua_kontak_cipher);

            if ($kontak === null || $kontak === '') {
                throw new RuntimeException('Kontak koordinator wilayah tidak dapat didekripsi.');
            }

            $wilayahId = (int) $wilayah->id;

            $rows[] = new WilayahAdminRowDto(
                id: $wilayahId,
                nama: (string) ($wilayah->nama ?? ''),
                koordinatorNama: (string) ($wilayah->ketua_nama ?? ''),
                koordinatorKontak: $kontak,
                lingkungan: $lingkunganByWilayah[$wilayahId] ?? [],
            );
        }

        return $rows;
    }

    public function getDetail(int $id): WilayahDetailDto
    {
        $wilayah = $this->wilayahRepository->findForDetail($id);

        if ($wilayah === null) {
            throw new DomainException('Wilayah tidak ditemukan.');
        }

        $kontak = $this->piiCipher->decrypt((string) $wilayah->ketua_kontak_cipher);

        if ($kontak === null || $kontak === '') {
            throw new RuntimeException('Kontak koordinator wilayah tidak dapat didekripsi.');
        }

        return new WilayahDetailDto(
            wilayah: $wilayah,
            ketuaKontak: $kontak,
            lingkungan: $this->wilayahRepository->findLingkunganForWilayah($id),
        );
    }

    public function getWithLingkungan(int $id): WilayahWithLingkunganDto
    {
        $result = $this->wilayahRepository->getWithLingkungan($id);

        if ($result === null) {
            throw new DomainException('Wilayah tidak ditemukan.');
        }

        return $result;
    }

    /**
     * @return list<WilayahWithLingkunganDto>
     */
    public function findAllWithLingkunganForPublic(): array
    {
        return $this->wilayahRepository->findAllWithLingkunganForPublic();
    }

    public function create(WilayahDto $dto): int
    {
        $kontakFields = $this->encryptRequiredKontak($dto->ketuaKontak);

        $id = $this->wilayahRepository->create([
            'nama'                => $dto->nama,
            'ketua_nama'          => $dto->ketuaNama,
            'ketua_kontak_cipher' => $kontakFields['cipher'],
            'ketua_kontak_hash'   => $kontakFields['hash'],
        ]);

        if ($id === false) {
            throw new RuntimeException('Gagal menyimpan wilayah.');
        }

        return (int) $id;
    }

    public function update(int $id, WilayahDto $dto): void
    {
        $existing = $this->wilayahRepository->find($id);

        if ($existing === null) {
            throw new DomainException('Wilayah tidak ditemukan.');
        }

        /** @var Wilayah $existing */
        $kontakFields = $this->resolveKontakFieldsForUpdate(
            existingCipher: (string) $existing->ketua_kontak_cipher,
            existingHash: (string) $existing->ketua_kontak_hash,
            newKontak: $dto->ketuaKontak,
            required: false,
        );

        if (! $this->wilayahRepository->update($id, [
            'nama'                => $dto->nama,
            'ketua_nama'          => $dto->ketuaNama,
            'ketua_kontak_cipher' => $kontakFields['cipher'],
            'ketua_kontak_hash'   => $kontakFields['hash'],
        ])) {
            throw new RuntimeException('Gagal memperbarui wilayah.');
        }
    }

    public function delete(int $id): void
    {
        if ($this->wilayahRepository->find($id) === null) {
            throw new DomainException('Wilayah tidak ditemukan.');
        }

        if (! $this->wilayahRepository->delete($id)) {
            throw new RuntimeException('Gagal menghapus wilayah.');
        }
    }

    /**
     * @return array{cipher: string, hash: string}
     */
    private function encryptRequiredKontak(string $kontak): array
    {
        $kontak = trim($kontak);

        if ($kontak === '') {
            throw new InvalidArgumentException('Nomor kontak koordinator wajib diisi.');
        }

        return [
            'cipher' => $this->piiCipher->encrypt($kontak),
            'hash'   => $this->piiCipher->hashPhone($kontak),
        ];
    }

    /**
     * @return array{cipher: string, hash: string}
     */
    private function resolveKontakFieldsForUpdate(
        string $existingCipher,
        string $existingHash,
        string $newKontak,
        bool $required,
    ): array {
        $newKontak = trim($newKontak);

        if ($newKontak === '') {
            if ($required) {
                throw new InvalidArgumentException('Nomor kontak koordinator wajib diisi.');
            }

            return [
                'cipher' => $existingCipher,
                'hash'   => $existingHash,
            ];
        }

        return [
            'cipher' => $this->piiCipher->encrypt($newKontak),
            'hash'   => $this->piiCipher->hashPhone($newKontak),
        ];
    }
}
