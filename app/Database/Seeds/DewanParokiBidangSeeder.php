<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DewanParokiBidangSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'kode'          => 'liturgi',
                'nama_tampilan' => 'Liturgi',
                'deskripsi'     => 'Bidang pelayanan perayaan Ekaristi, liturgi, musik liturgi, dan misa paroki.',
                'icon'          => 'liturgi',
                'urutan'        => 1,
            ],
            [
                'kode'          => 'diakonia',
                'nama_tampilan' => 'Diakonia',
                'deskripsi'     => 'Bidang pelayanan sosial, kemanusiaan, dan bantuan kepada umat yang membutuhkan.',
                'icon'          => 'diakonia',
                'urutan'        => 2,
            ],
            [
                'kode'          => 'koinonia',
                'nama_tampilan' => 'Koinonia',
                'deskripsi'     => 'Bidang pembinaan persekutuan, kehidupan komunitas, dan kegiatan umat berbasis wilayah.',
                'icon'          => 'koinonia',
                'urutan'        => 3,
            ],
            [
                'kode'          => 'kerygma',
                'nama_tampilan' => 'Kerygma',
                'deskripsi'     => 'Bidang penginjilan, katekese, pembinaan iman, dan pelayanan pastoral umat.',
                'icon'          => 'kerygma',
                'urutan'        => 4,
            ],
        ];

        $this->db->table('dewan_paroki_bidang')->insertBatch($data);
    }
}
