<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\I18n\Time;

class RestructureGaleriTables extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'judul'      => ['type' => 'VARCHAR', 'constraint' => 255],
            'slug'       => ['type' => 'VARCHAR', 'constraint' => 255],
            'urutan'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug');
        $this->forge->addKey('urutan');
        $this->forge->addKey('deleted_at');
        $this->forge->createTable('galeri_event', true, $this->tableAttributes());

        $this->forge->addColumn('galeri', [
            'galeri_event_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'id',
            ],
            'jenis' => [
                'type'       => 'ENUM',
                'constraint' => ['foto', 'video'],
                'default'    => 'foto',
                'after'      => 'galeri_event_id',
            ],
            'youtube_url' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
                'after'      => 'file_path',
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'created_at',
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'updated_at',
            ],
        ]);

        $this->db->query('ALTER TABLE galeri MODIFY file_path VARCHAR(500) NULL');

        $now = Time::now()->toDateTimeString();
        $this->db->table('galeri_event')->insert([
            'judul'      => 'Arsip',
            'slug'       => 'arsip',
            'urutan'     => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $defaultEventId = (int) $this->db->insertID();

        $this->db->table('galeri')->update([
            'galeri_event_id' => $defaultEventId,
            'jenis'           => 'foto',
        ]);

        $this->forge->modifyColumn('galeri', [
            'galeri_event_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
        ]);

        $this->forge->addForeignKey('galeri_event_id', 'galeri_event', 'id', 'CASCADE', 'CASCADE', 'galeri_event_fk');
        $this->forge->processIndexes('galeri');
    }

    public function down(): void
    {
        $this->forge->dropForeignKey('galeri', 'galeri_event_fk');
        $this->forge->dropColumn('galeri', ['galeri_event_id', 'jenis', 'youtube_url', 'updated_at', 'deleted_at']);
        $this->db->query('ALTER TABLE galeri MODIFY file_path VARCHAR(500) NOT NULL');
        $this->forge->dropTable('galeri_event', true);
    }

    /**
     * @return array<string, string>
     */
    private function tableAttributes(): array
    {
        return $this->db->getPlatform() === 'MySQLi' ? ['ENGINE' => 'InnoDB'] : [];
    }
}
