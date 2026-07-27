<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePendaftaranTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nama_lengkap'      => ['type' => 'VARCHAR', 'constraint' => 255],
            'whatsapp_cipher'   => ['type' => 'TEXT'],
            'whatsapp_hash'     => ['type' => 'VARCHAR', 'constraint' => 64],
            'sakramen_jenis_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'pesan'             => ['type' => 'TEXT', 'null' => true],
            'status'            => [
                'type'       => 'ENUM',
                'constraint' => ['baru', 'diproses', 'selesai', 'ditolak'],
                'default'    => 'baru',
            ],
            'created_at'        => ['type' => 'DATETIME', 'null' => true],
            'updated_at'        => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('sakramen_jenis_id');
        $this->forge->addKey('status');
        $this->forge->addKey('created_at');
        $this->forge->addForeignKey('sakramen_jenis_id', 'sakramen_jenis', 'id', 'RESTRICT', 'SET NULL');
        $this->forge->createTable('pendaftaran', true, $this->tableAttributes());
    }

    public function down(): void
    {
        $this->forge->dropTable('pendaftaran', true);
    }

    /**
     * @return array<string, string>
     */
    private function tableAttributes(): array
    {
        return $this->db->getPlatform() === 'MySQLi' ? ['ENGINE' => 'InnoDB'] : [];
    }
}
