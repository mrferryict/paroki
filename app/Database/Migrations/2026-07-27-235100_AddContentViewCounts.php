<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddContentViewCounts extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('berita', [
            'view_count' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0,
                'after'      => 'tanggal_terbit',
            ],
        ]);

        $this->forge->addColumn('artikel', [
            'view_count' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0,
                'after'      => 'tanggal_terbit',
            ],
        ]);

        $this->forge->addColumn('galeri_event', [
            'view_count' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0,
                'after'      => 'urutan',
            ],
        ]);

        $this->forge->addColumn('dokumen', [
            'download_count' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0,
                'after'      => 'kategori',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('berita', 'view_count');
        $this->forge->dropColumn('artikel', 'view_count');
        $this->forge->dropColumn('galeri_event', 'view_count');
        $this->forge->dropColumn('dokumen', 'download_count');
    }
}
