<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDewanParokiPenjabatTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'bidang_id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'nama'              => ['type' => 'VARCHAR', 'constraint' => 255],
            'whatsapp_cipher'   => ['type' => 'TEXT'],
            'whatsapp_hash'     => ['type' => 'VARCHAR', 'constraint' => 64],
            'urutan'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
            'created_at'        => ['type' => 'DATETIME', 'null' => true],
            'updated_at'        => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'        => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('bidang_id');
        $this->forge->addKey('urutan');
        $this->forge->addKey('deleted_at');
        $this->forge->addForeignKey('bidang_id', 'dewan_paroki_bidang', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('dewan_paroki_penjabat', true, $this->tableAttributes());
    }

    public function down(): void
    {
        $this->forge->dropTable('dewan_paroki_penjabat', true);
    }

    /**
     * @return array<string, string>
     */
    private function tableAttributes(): array
    {
        return $this->db->getPlatform() === 'MySQLi' ? ['ENGINE' => 'InnoDB'] : [];
    }
}
