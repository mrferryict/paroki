<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGaleriTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'file_path'  => ['type' => 'VARCHAR', 'constraint' => 500],
            'caption'    => ['type' => 'TEXT', 'null' => true],
            'urutan'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('urutan');
        $this->forge->createTable('galeri', true, $this->tableAttributes());
    }

    public function down(): void
    {
        $this->forge->dropTable('galeri', true);
    }

    /**
     * @return array<string, string>
     */
    private function tableAttributes(): array
    {
        return $this->db->getPlatform() === 'MySQLi' ? ['ENGINE' => 'InnoDB'] : [];
    }
}
