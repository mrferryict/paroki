<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Pendaftaran\PaginatedPendaftaranListDto;
use App\DTOs\Pendaftaran\PendaftaranDetailDto;
use App\DTOs\Pendaftaran\PendaftaranDto;
use App\DTOs\Pendaftaran\PendaftaranListFilterDto;
use App\Entities\Pendaftaran;
use App\Enums\PendaftaranStatus;
use App\Libraries\PiiCipher;
use App\Models\SakramenJenisModel;
use App\Repositories\PendaftaranRepository;
use DomainException;
use InvalidArgumentException;
use RuntimeException;

class PendaftaranService
{
    public function __construct(
        private readonly PendaftaranRepository $pendaftaranRepository,
        private readonly PiiCipher $piiCipher,
        private readonly SakramenJenisModel $sakramenJenisModel,
    ) {}

    /**
     * @return array<string, string>
     */
    public function statusOptions(): array
    {
        return PendaftaranStatus::options();
    }

    public function findPaginatedForAdmin(PendaftaranListFilterDto $filter): PaginatedPendaftaranListDto
    {
        return $this->pendaftaranRepository->findPaginatedForAdmin($filter);
    }

    public function getDetail(int $id): PendaftaranDetailDto
    {
        $pendaftaran = $this->pendaftaranRepository->findByIdForDetail($id);

        if ($pendaftaran === null) {
            throw new DomainException('Pendaftaran tidak ditemukan.');
        }

        $whatsapp = $this->piiCipher->decrypt((string) $pendaftaran->whatsapp_cipher);

        if ($whatsapp === null || $whatsapp === '') {
            throw new RuntimeException('Nomor WhatsApp tidak dapat didekripsi.');
        }

        return new PendaftaranDetailDto(
            pendaftaran: $pendaftaran,
            whatsapp: $whatsapp,
            sakramenNama: $this->pendaftaranRepository->resolveSakramenNamaFromEntity($pendaftaran),
        );
    }

    public function submit(PendaftaranDto $dto): int
    {
        $this->assertValidSakramenJenisId($dto->sakramenJenisId);

        $whatsapp = trim($dto->whatsapp);

        if ($whatsapp === '') {
            throw new InvalidArgumentException('Nomor WhatsApp wajib diisi.');
        }

        $id = $this->pendaftaranRepository->create([
            'nama_lengkap'      => trim($dto->namaLengkap),
            'whatsapp_cipher'   => $this->piiCipher->encrypt($whatsapp),
            'whatsapp_hash'     => $this->piiCipher->hashPhone($whatsapp),
            'sakramen_jenis_id' => $dto->sakramenJenisId,
            'pesan'             => $dto->pesan,
            'status'            => PendaftaranStatus::Baru->value,
        ]);

        if ($id === false) {
            throw new RuntimeException('Gagal menyimpan pendaftaran.');
        }

        return (int) $id;
    }

    public function updateStatus(int $id, PendaftaranStatus $newStatus): void
    {
        $existing = $this->pendaftaranRepository->find($id);

        if ($existing === null) {
            throw new DomainException('Pendaftaran tidak ditemukan.');
        }

        /** @var Pendaftaran $existing */
        $currentStatus = $existing->status instanceof PendaftaranStatus
            ? $existing->status
            : PendaftaranStatus::from((string) $existing->status);

        $this->assertAllowedStatusTransition(from: $currentStatus, to: $newStatus);

        $db = db_connect();
        $db->transStart();

        if (! $this->pendaftaranRepository->update($id, ['status' => $newStatus->value])) {
            $db->transRollback();

            throw new RuntimeException('Gagal memperbarui status pendaftaran.');
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new RuntimeException('Gagal memperbarui status pendaftaran.');
        }
    }

    /**
     * @return list<PendaftaranStatus>
     */
    public function getAllowedNextStatuses(PendaftaranStatus $current): array
    {
        return match ($current) {
            PendaftaranStatus::Baru     => [PendaftaranStatus::Diproses, PendaftaranStatus::Ditolak],
            PendaftaranStatus::Diproses => [PendaftaranStatus::Selesai, PendaftaranStatus::Ditolak],
            PendaftaranStatus::Selesai,
            PendaftaranStatus::Ditolak  => [],
        };
    }

    private function assertValidSakramenJenisId(?int $sakramenJenisId): void
    {
        if ($sakramenJenisId === null) {
            return;
        }

        if ($this->sakramenJenisModel->find($sakramenJenisId) === null) {
            throw new InvalidArgumentException('Jenis layanan tidak valid.');
        }
    }

    private function assertAllowedStatusTransition(PendaftaranStatus $from, PendaftaranStatus $to): void
    {
        if ($from === $to) {
            return;
        }

        $allowed = $this->getAllowedNextStatuses($from);

        if (! in_array($to, $allowed, true)) {
            throw new DomainException(
                sprintf(
                    'Status tidak dapat diubah dari %s ke %s.',
                    $from->label(),
                    $to->label(),
                ),
            );
        }
    }
}
