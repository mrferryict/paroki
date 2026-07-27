<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWilayahTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'                   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nama'                 => ['type' => 'VARCHAR', 'constraint' => 255],
            'ketua_nama'           => ['type' => 'VARCHAR', 'constraint' => 255],
            'ketua_kontak_cipher'  => ['type' => 'TEXT'],
            'ketua_kontak_hash'    => ['type' => 'VARCHAR', 'constraint' => 64],
            'created_at'           => ['type' => 'DATETIME', 'null' => true],
            'updated_at'           => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'           => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('deleted_at');
        $this->forge->createTable('wilayah', true, $this->tableAttributes());
    }

    public function down(): void
    {
        $this->forge->dropTable('wilayah', true);
    }

    /**
     * @return array<string, string>
     */
    private function tableAttributes(): array
    {
        return $this->db->getPlatform() === 'MySQLi' ? ['ENGINE' => 'InnoDB'] : [];
    }
}
