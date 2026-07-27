<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SakramenJenisSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'kode'      => 'baptis',
                'nama'      => 'Sakramen Baptis',
                'deskripsi' => 'Pendaftaran dan persiapan baptis bayi, anak, maupun dewasa.',
                'icon'      => 'baptis',
                'urutan'    => 1,
                'is_active' => 1,
            ],
            [
                'kode'      => 'komuni_pertama',
                'nama'      => 'Komuni Pertama',
                'deskripsi' => 'Persiapan dan pendaftaran Sakramen Ekaristi Pertama.',
                'icon'      => 'komuni-pertama',
                'urutan'    => 2,
                'is_active' => 1,
            ],
            [
                'kode'      => 'krisma',
                'nama'      => 'Sakramen Krisma',
                'deskripsi' => 'Persiapan dan pendaftaran Sakramen Penguatan (Krisma).',
                'icon'      => 'krisma',
                'urutan'    => 3,
                'is_active' => 1,
            ],
            [
                'kode'      => 'tobat',
                'nama'      => 'Sakramen Tobat',
                'deskripsi' => 'Permohonan bimbingan rohani dan rekonsiliasi (Sakramen Tobat).',
                'icon'      => 'tobat',
                'urutan'    => 4,
                'is_active' => 1,
            ],
            [
                'kode'      => 'perkawinan',
                'nama'      => 'Sakramen Perkawinan',
                'deskripsi' => 'Persiapan dan pendaftaran perkawinan Katolik di paroki.',
                'icon'      => 'perkawinan',
                'urutan'    => 5,
                'is_active' => 1,
            ],
            [
                'kode'      => 'pengurapan_orang_sakit',
                'nama'      => 'Pengurapan Orang Sakit',
                'deskripsi' => 'Permohonan kunjungan pastoral dan Sakramen Pengurapan Orang Sakit.',
                'icon'      => 'pengurapan',
                'urutan'    => 6,
                'is_active' => 1,
            ],
            [
                'kode'      => 'misdinar',
                'nama'      => 'Misdinar',
                'deskripsi' => 'Pendaftaran dan pembinaan pelayanan liturgi sebagai misdinar.',
                'icon'      => 'misdinar',
                'urutan'    => 7,
                'is_active' => 1,
            ],
            [
                'kode'      => 'konsultasi_psikologi',
                'nama'      => 'Konsultasi Psikologi',
                'deskripsi' => 'Permohonan bimbingan psikologi pastoral untuk umat paroki.',
                'icon'      => 'konsultasi-psikologi',
                'urutan'    => 8,
                'is_active' => 1,
            ],
            [
                'kode'      => 'konsultasi_hukum',
                'nama'      => 'Konsultasi Hukum',
                'deskripsi' => 'Permohonan bimbingan hukum gerejawi dan administrasi perkawinan.',
                'icon'      => 'konsultasi-hukum',
                'urutan'    => 9,
                'is_active' => 1,
            ],
            [
                'kode'      => 'administrasi',
                'nama'      => 'Administrasi Paroki',
                'deskripsi' => 'Permohonan surat baptis, surat nikah, surat keterangan, dan dokumen paroki lainnya.',
                'icon'      => 'administrasi',
                'urutan'    => 10,
                'is_active' => 1,
            ],
        ];

        $this->db->table('sakramen_jenis')->insertBatch($data);
    }
}
