<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateHeroSlideTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'eyebrow'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'judul'      => ['type' => 'TEXT'],
            'subjudul'   => ['type' => 'TEXT', 'null' => true],
            'cta1_label' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'cta1_href'  => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'cta2_label' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'cta2_href'  => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'gambar'     => ['type' => 'VARCHAR', 'constraint' => 500],
            'urutan'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
            'is_active'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('urutan');
        $this->forge->addKey('is_active');
        $this->forge->createTable('hero_slide', true, $this->tableAttributes());
    }

    public function down(): void
    {
        $this->forge->dropTable('hero_slide', true);
    }

    /**
     * @return array<string, string>
     */
    private function tableAttributes(): array
    {
        return $this->db->getPlatform() === 'MySQLi' ? ['ENGINE' => 'InnoDB'] : [];
    }
}
