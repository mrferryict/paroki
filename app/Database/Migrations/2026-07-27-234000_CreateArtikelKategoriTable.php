<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\I18n\Time;

class CreateArtikelKategoriTable extends Migration
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
        $this->forge->createTable('artikel_kategori', true, $this->tableAttributes());

        $now = Time::now()->toDateTimeString();

        $defaults = [
            ['slug' => 'artikel_iman', 'label' => 'Artikel Iman', 'urutan' => 1],
            ['slug' => 'renungan_harian', 'label' => 'Renungan Harian', 'urutan' => 2],
            ['slug' => 'orang_kudus', 'label' => 'Orang Kudus', 'urutan' => 3],
            ['slug' => 'mutiara_biblika', 'label' => 'Mutiara Biblika', 'urutan' => 4],
        ];

        foreach ($defaults as $row) {
            $this->db->table('artikel_kategori')->insert([
                ...$row,
                'is_active'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $this->db->query(
            'ALTER TABLE artikel MODIFY kategori VARCHAR(100) NOT NULL',
        );
    }

    public function down(): void
    {
        $this->db->query(
            "ALTER TABLE artikel MODIFY kategori ENUM('artikel_iman','renungan_harian','orang_kudus','mutiara_biblika') NOT NULL",
        );
        $this->forge->dropTable('artikel_kategori', true);
    }

    /**
     * @return array<string, string>
     */
    private function tableAttributes(): array
    {
        return $this->db->getPlatform() === 'MySQLi' ? ['ENGINE' => 'InnoDB'] : [];
    }
}
