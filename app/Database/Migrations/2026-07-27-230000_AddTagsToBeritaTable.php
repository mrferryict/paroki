<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTagsToBeritaTable extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('berita', [
            'tags' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
                'after'      => 'kategori',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('berita', 'tags');
    }
}
