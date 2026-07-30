<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSiteBrandingFieldsToSiteSetting extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('site_setting', [
            'site_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
                'default'    => 'Paroki Hati Kudus Yesus',
                'after'      => 'logo_path',
            ],
            'copyright_text' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => false,
                'default'    => 'Semua hak dilindungi.',
                'after'      => 'site_name',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('site_setting', ['site_name', 'copyright_text']);
    }
}
