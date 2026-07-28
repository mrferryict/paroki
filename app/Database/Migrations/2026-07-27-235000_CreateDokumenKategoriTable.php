<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\I18n\Time;

class CreateDokumenKategoriTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'slug'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'label'      => ['type' => 'VARCHAR', 'constraint' => 255],
            'urutan'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
            'is_active'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug');
        $this->forge->addKey('urutan');
        $this->forge->addKey('is_active');
        $this->forge->addKey('deleted_at');
        $this->forge->createTable('dokumen_kategori', true, $this->tableAttributes());

        $now = Time::now()->toDateTimeString();

        $defaults = [
            ['slug' => 'formulir', 'label' => 'Formulir', 'urutan' => 1],
            ['slug' => 'warta_paroki', 'label' => 'Warta Paroki', 'urutan' => 2],
            ['slug' => 'majalah', 'label' => 'Majalah', 'urutan' => 3],
            ['slug' => 'dokumen', 'label' => 'Dokumen', 'urutan' => 4],
        ];

        foreach ($defaults as $row) {
            $this->db->table('dokumen_kategori')->insert([
                ...$row,
                'is_active'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        $this->forge->dropTable('dokumen_kategori', true);
    }

    /**
     * @return array<string, string>
     */
    private function tableAttributes(): array
    {
        return $this->db->getPlatform() === 'MySQLi' ? ['ENGINE' => 'InnoDB'] : [];
    }
}
