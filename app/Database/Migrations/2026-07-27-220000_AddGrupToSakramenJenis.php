<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddGrupToSakramenJenis extends Migration
{
    public function up(): void
    {
        $kodeValues = [
            'baptis',
            'komuni_pertama',
            'krisma',
            'tobat',
            'perkawinan',
            'pengurapan_orang_sakit',
            'imamat',
            'konsultasi_psikologi',
            'konsultasi_hukum',
            'administrasi',
            'misdinar',
            'pemazmur',
            'prodiakon',
            'organis',
        ];

        $this->forge->modifyColumn('sakramen_jenis', [
            'kode' => [
                'type'       => 'ENUM',
                'constraint' => $kodeValues,
            ],
        ]);

        $this->forge->addColumn('sakramen_jenis', [
            'grup' => [
                'type'       => 'ENUM',
                'constraint' => ['sakramen', 'konsultasi', 'administrasi', 'petugas'],
                'null'       => true,
                'after'      => 'kode',
            ],
        ]);

        $grupByKode = [
            'baptis'                 => 'sakramen',
            'komuni_pertama'         => 'sakramen',
            'krisma'                 => 'sakramen',
            'tobat'                  => 'sakramen',
            'perkawinan'             => 'sakramen',
            'pengurapan_orang_sakit' => 'sakramen',
            'imamat'                 => 'sakramen',
            'konsultasi_psikologi'   => 'konsultasi',
            'konsultasi_hukum'       => 'konsultasi',
            'administrasi'           => 'administrasi',
            'misdinar'               => 'petugas',
            'pemazmur'               => 'petugas',
            'prodiakon'              => 'petugas',
            'organis'                => 'petugas',
        ];

        foreach ($grupByKode as $kode => $grup) {
            $this->db->table('sakramen_jenis')->where('kode', $kode)->update(['grup' => $grup]);
        }

        $this->db->query('UPDATE sakramen_jenis SET grup = "sakramen" WHERE grup IS NULL');

        $this->forge->modifyColumn('sakramen_jenis', [
            'grup' => [
                'type'       => 'ENUM',
                'constraint' => ['sakramen', 'konsultasi', 'administrasi', 'petugas'],
                'null'       => false,
                'after'      => 'kode',
            ],
        ]);

        $this->seedMissingRows();
        $this->refreshNamesAndOrder();
    }

    public function down(): void
    {
        $this->db->table('sakramen_jenis')->whereIn('kode', ['imamat', 'pemazmur', 'prodiakon', 'organis'])->delete();

        $this->forge->dropColumn('sakramen_jenis', 'grup');

        $this->forge->modifyColumn('sakramen_jenis', [
            'kode' => [
                'type'       => 'ENUM',
                'constraint' => [
                    'baptis',
                    'komuni_pertama',
                    'krisma',
                    'tobat',
                    'perkawinan',
                    'pengurapan_orang_sakit',
                    'misdinar',
                    'konsultasi_psikologi',
                    'konsultasi_hukum',
                    'administrasi',
                ],
            ],
        ]);
    }

    private function seedMissingRows(): void
    {
        $rows = [
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

        foreach ($rows as $row) {
            $exists = $this->db->table('sakramen_jenis')->where('kode', $row['kode'])->countAllResults();

            if ($exists === 0) {
                $this->db->table('sakramen_jenis')->insert($row);
            }
        }
    }

    private function refreshNamesAndOrder(): void
    {
        $updates = [
            'administrasi' => ['nama' => 'Administrasi (Sekretariat)', 'grup' => 'administrasi', 'urutan' => 11],
            'misdinar'     => ['grup' => 'petugas', 'urutan' => 12],
            'konsultasi_psikologi' => ['grup' => 'konsultasi', 'urutan' => 9],
            'konsultasi_hukum'     => ['grup' => 'konsultasi', 'urutan' => 10],
        ];

        foreach ($updates as $kode => $data) {
            $this->db->table('sakramen_jenis')->where('kode', $kode)->update($data);
        }
    }
}
