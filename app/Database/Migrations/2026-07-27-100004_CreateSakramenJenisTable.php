<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSakramenJenisTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'kode'       => [
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
            'nama'       => ['type' => 'VARCHAR', 'constraint' => 255],
            'deskripsi'  => ['type' => 'TEXT', 'null' => true],
            'icon'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'urutan'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
            'is_active'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('kode');
        $this->forge->addKey('urutan');
        $this->forge->addKey('is_active');
        $this->forge->addKey('deleted_at');
        $this->forge->createTable('sakramen_jenis', true, $this->tableAttributes());
    }

    public function down(): void
    {
        $this->forge->dropTable('sakramen_jenis', true);
    }

    /**
     * @return array<string, string>
     */
    private function tableAttributes(): array
    {
        return $this->db->getPlatform() === 'MySQLi' ? ['ENGINE' => 'InnoDB'] : [];
    }
}
