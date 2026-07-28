<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SakramenJenisSeeder extends Seeder
{
    public function run(): void
    {
        if ($this->db->table('sakramen_jenis')->countAllResults() > 0) {
            return;
        }

        $data = [
            [
                'kode'      => 'baptis',
                'grup'      => 'sakramen',
                'nama'      => 'Sakramen Baptis',
                'deskripsi' => 'Pendaftaran dan persiapan baptis bayi, anak, maupun dewasa.',
                'icon'      => 'baptis',
                'urutan'    => 1,
                'is_active' => 1,
            ],
            [
                'kode'      => 'komuni_pertama',
                'grup'      => 'sakramen',
                'nama'      => 'Komuni Pertama',
                'deskripsi' => 'Persiapan dan pendaftaran Sakramen Ekaristi Pertama.',
                'icon'      => 'komuni-pertama',
                'urutan'    => 2,
                'is_active' => 1,
            ],
            [
                'kode'      => 'krisma',
                'grup'      => 'sakramen',
                'nama'      => 'Sakramen Krisma',
                'deskripsi' => 'Persiapan dan pendaftaran Sakramen Penguatan (Krisma).',
                'icon'      => 'krisma',
                'urutan'    => 3,
                'is_active' => 1,
            ],
            [
                'kode'      => 'tobat',
                'grup'      => 'sakramen',
                'nama'      => 'Sakramen Tobat',
                'deskripsi' => 'Permohonan bimbingan rohani dan rekonsiliasi (Sakramen Tobat).',
                'icon'      => 'tobat',
                'urutan'    => 4,
                'is_active' => 1,
            ],
            [
                'kode'      => 'perkawinan',
                'grup'      => 'sakramen',
                'nama'      => 'Sakramen Perkawinan',
                'deskripsi' => 'Persiapan dan pendaftaran perkawinan Katolik di paroki.',
                'icon'      => 'perkawinan',
                'urutan'    => 5,
                'is_active' => 1,
            ],
            [
                'kode'      => 'pengurapan_orang_sakit',
                'grup'      => 'sakramen',
                'nama'      => 'Pengurapan Orang Sakit',
                'deskripsi' => 'Permohonan kunjungan pastoral dan Sakramen Pengurapan Orang Sakit.',
                'icon'      => 'pengurapan',
                'urutan'    => 6,
                'is_active' => 1,
            ],
            [
                'kode'      => 'imamat',
                'grup'      => 'sakramen',
                'nama'      => 'Sakramen Imamat',
                'deskripsi' => 'Informasi dan bimbingan terkait panggilan serta persiapan Sakramen Imamat.',
                'icon'      => 'imamat',
                'urutan'    => 7,
                'is_active' => 1,
            ],
            [
                'kode'      => 'konsultasi_psikologi',
                'grup'      => 'konsultasi',
                'nama'      => 'Konsultasi Psikologi',
                'deskripsi' => 'Permohonan bimbingan psikologi pastoral untuk umat paroki.',
                'icon'      => 'konsultasi-psikologi',
                'urutan'    => 8,
                'is_active' => 1,
            ],
            [
                'kode'      => 'konsultasi_hukum',
                'grup'      => 'konsultasi',
                'nama'      => 'Konsultasi Hukum',
                'deskripsi' => 'Permohonan bimbingan hukum gerejawi dan administrasi perkawinan.',
                'icon'      => 'konsultasi-hukum',
                'urutan'    => 9,
                'is_active' => 1,
            ],
            [
                'kode'      => 'administrasi',
                'grup'      => 'administrasi',
                'nama'      => 'Administrasi (Sekretariat)',
                'deskripsi' => 'Permohonan surat baptis, surat nikah, surat keterangan, dan dokumen paroki lainnya.',
                'icon'      => 'administrasi',
                'urutan'    => 10,
                'is_active' => 1,
            ],
            [
                'kode'      => 'misdinar',
                'grup'      => 'petugas',
                'nama'      => 'Misdinar',
                'deskripsi' => 'Pendaftaran dan pembinaan pelayanan liturgi sebagai misdinar.',
                'icon'      => 'misdinar',
                'urutan'    => 11,
                'is_active' => 1,
            ],
            [
                'kode'      => 'pemazmur',
                'grup'      => 'petugas',
                'nama'      => 'Pemazmur',
                'deskripsi' => 'Pendaftaran dan pembinaan umat sebagai pemazmur liturgi.',
                'icon'      => 'pemazmur',
                'urutan'    => 12,
                'is_active' => 1,
            ],
            [
                'kode'      => 'prodiakon',
                'grup'      => 'petugas',
                'nama'      => 'Prodiakon',
                'deskripsi' => 'Pendaftaran dan pembinaan pelayanan sebagai prodiakon paroki.',
                'icon'      => 'prodiakon',
                'urutan'    => 13,
                'is_active' => 1,
            ],
            [
                'kode'      => 'organis',
                'grup'      => 'petugas',
                'nama'      => 'Organis',
                'deskripsi' => 'Pendaftaran dan pembinaan umat sebagai organist liturgi.',
                'icon'      => 'organis',
                'urutan'    => 14,
                'is_active' => 1,
            ],
        ];

        $this->db->table('sakramen_jenis')->insertBatch($data);
    }
}
