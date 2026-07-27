<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBeritaTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'judul'          => ['type' => 'VARCHAR', 'constraint' => 255],
            'slug'           => ['type' => 'VARCHAR', 'constraint' => 255],
            'kategori'       => [
                'type'       => 'ENUM',
                'constraint' => [
                    'pengumuman',
                    'kegiatan_paroki',
                    'pelayanan_sosial',
                    'kegiatan_wilayah',
                    'liturgi',
                ],
            ],
            'ringkasan'      => ['type' => 'TEXT', 'null' => true],
            'konten'         => ['type' => 'LONGTEXT', 'null' => true],
            'gambar_utama'   => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'status'         => [
                'type'       => 'ENUM',
                'constraint' => ['draft', 'terbit'],
                'default'    => 'draft',
            ],
            'tanggal_terbit' => ['type' => 'DATETIME', 'null' => true],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug');
        $this->forge->addKey('kategori');
        $this->forge->addKey('status');
        $this->forge->addKey('tanggal_terbit');
        $this->forge->addKey('deleted_at');
        $this->forge->createTable('berita', true, $this->tableAttributes());
    }

    public function down(): void
    {
        $this->forge->dropTable('berita', true);
    }

    /**
     * @return array<string, string>
     */
    private function tableAttributes(): array
    {
        return $this->db->getPlatform() === 'MySQLi' ? ['ENGINE' => 'InnoDB'] : [];
    }
}
