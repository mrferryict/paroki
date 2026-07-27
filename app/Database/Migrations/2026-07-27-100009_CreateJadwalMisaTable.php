<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJadwalMisaTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'jenis'      => [
                'type'       => 'ENUM',
                'constraint' => ['harian', 'mingguan', 'jumat_pertama', 'khusus'],
            ],
            'hari_label' => ['type' => 'VARCHAR', 'constraint' => 100],
            'jam'        => ['type' => 'TIME'],
            'catatan'    => ['type' => 'TEXT', 'null' => true],
            'urutan'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
            'is_active'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('jenis');
        $this->forge->addKey('urutan');
        $this->forge->addKey('is_active');
        $this->forge->addKey('deleted_at');
        $this->forge->createTable('jadwal_misa', true, $this->tableAttributes());
    }

    public function down(): void
    {
        $this->forge->dropTable('jadwal_misa', true);
    }

    /**
     * @return array<string, string>
     */
    private function tableAttributes(): array
    {
        return $this->db->getPlatform() === 'MySQLi' ? ['ENGINE' => 'InnoDB'] : [];
    }
}
