<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Lingkungan\LingkunganDetailDto;
use App\DTOs\Lingkungan\LingkunganDto;
use App\Entities\Lingkungan;
use App\Libraries\PiiCipher;
use App\Repositories\LingkunganRepository;
use App\Repositories\WilayahRepository;
use DomainException;
use RuntimeException;

class LingkunganService
{
    public function __construct(
        private readonly LingkunganRepository $lingkunganRepository,
        private readonly WilayahRepository $wilayahRepository,
        private readonly PiiCipher $piiCipher,
    ) {}

    /**
     * @return list<Lingkungan>
     */
    public function findAllByWilayahIdForList(int $wilayahId): array
    {
        $this->assertWilayahExists($wilayahId);

        return $this->lingkunganRepository->findAllByWilayahIdForList($wilayahId);
    }

    public function getDetail(int $id): LingkunganDetailDto
    {
        $lingkungan = $this->lingkunganRepository->find($id);

        if ($lingkungan === null) {
            throw new DomainException('Lingkungan tidak ditemukan.');
        }

        /** @var Lingkungan $lingkungan */
        $kontak = null;

        if ($lingkungan->ketua_kontak_cipher !== null && $lingkungan->ketua_kontak_cipher !== '') {
            $kontak = $this->piiCipher->decrypt((string) $lingkungan->ketua_kontak_cipher);
        }

        return new LingkunganDetailDto(
            lingkungan: $lingkungan,
            ketuaKontak: $kontak,
        );
    }

    public function create(LingkunganDto $dto): int
    {
        $this->assertWilayahExists($dto->wilayahId);

        $kontakFields = $this->encryptOptionalKontak($dto->ketuaKontak);

        $id = $this->lingkunganRepository->create([
            'wilayah_id'          => $dto->wilayahId,
            'nama'                => $dto->nama,
            'ketua_nama'          => $dto->ketuaNama,
            'ketua_kontak_cipher' => $kontakFields['cipher'],
            'ketua_kontak_hash'   => $kontakFields['hash'],
        ]);

        if ($id === false) {
            throw new RuntimeException('Gagal menyimpan lingkungan.');
        }

        return (int) $id;
    }

    public function update(int $id, LingkunganDto $dto): void
    {
        $existing = $this->lingkunganRepository->find($id);

        if ($existing === null) {
            throw new DomainException('Lingkungan tidak ditemukan.');
        }

        /** @var Lingkungan $existing */
        $this->assertWilayahExists($dto->wilayahId);

        $kontakFields = $this->resolveOptionalKontakForUpdate(
            existingCipher: $existing->ketua_kontak_cipher,
            existingHash: $existing->ketua_kontak_hash,
            newKontak: $dto->ketuaKontak,
        );

        if (! $this->lingkunganRepository->update($id, [
            'wilayah_id'          => $dto->wilayahId,
            'nama'                => $dto->nama,
            'ketua_nama'          => $dto->ketuaNama,
            'ketua_kontak_cipher' => $kontakFields['cipher'],
            'ketua_kontak_hash'   => $kontakFields['hash'],
        ])) {
            throw new RuntimeException('Gagal memperbarui lingkungan.');
        }
    }

    public function delete(int $id): void
    {
        if ($this->lingkunganRepository->find($id) === null) {
            throw new DomainException('Lingkungan tidak ditemukan.');
        }

        if (! $this->lingkunganRepository->delete($id)) {
            throw new RuntimeException('Gagal menghapus lingkungan.');
        }
    }

    private function assertWilayahExists(int $wilayahId): void
    {
        if ($this->wilayahRepository->find($wilayahId) === null) {
            throw new DomainException('Wilayah tidak ditemukan.');
        }
    }

    /**
     * @return array{cipher: ?string, hash: ?string}
     */
    private function encryptOptionalKontak(?string $kontak): array
    {
        if ($kontak === null || trim($kontak) === '') {
            return ['cipher' => null, 'hash' => null];
        }

        $kontak = trim($kontak);

        return [
            'cipher' => $this->piiCipher->encrypt($kontak),
            'hash'   => $this->piiCipher->hashPhone($kontak),
        ];
    }

    /**
     * @return array{cipher: ?string, hash: ?string}
     */
    private function resolveOptionalKontakForUpdate(
        ?string $existingCipher,
        ?string $existingHash,
        ?string $newKontak,
    ): array {
        if ($newKontak === null || trim($newKontak) === '') {
            return [
                'cipher' => $existingCipher,
                'hash'   => $existingHash,
            ];
        }

        $newKontak = trim($newKontak);

        return [
            'cipher' => $this->piiCipher->encrypt($newKontak),
            'hash'   => $this->piiCipher->hashPhone($newKontak),
        ];
    }
}
