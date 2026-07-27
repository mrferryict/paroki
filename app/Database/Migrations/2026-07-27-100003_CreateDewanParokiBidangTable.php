<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDewanParokiBidangTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'kode'          => [
                'type'       => 'ENUM',
                'constraint' => ['liturgi', 'diakonia', 'koinonia', 'kerygma'],
            ],
            'nama_tampilan' => ['type' => 'VARCHAR', 'constraint' => 255],
            'deskripsi'     => ['type' => 'TEXT', 'null' => true],
            'icon'          => ['type' => 'VARCHAR', 'constraint' => 100],
            'urutan'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
            'deleted_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('kode');
        $this->forge->addKey('urutan');
        $this->forge->addKey('deleted_at');
        $this->forge->createTable('dewan_paroki_bidang', true, $this->tableAttributes());
    }

    public function down(): void
    {
        $this->forge->dropTable('dewan_paroki_bidang', true);
    }

    /**
     * @return array<string, string>
     */
    private function tableAttributes(): array
    {
        return $this->db->getPlatform() === 'MySQLi' ? ['ENGINE' => 'InnoDB'] : [];
    }
}
