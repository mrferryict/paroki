<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenameSiteNameToSantoMikaelGombong extends Migration
{
    private const NEW_NAME = 'Paroki Santo Mikael Gombong';

    private const OLD_NAME = 'Paroki Hati Kudus Yesus';

    public function up(): void
    {
        $this->db->table('site_setting')
            ->where('id', 1)
            ->update(['site_name' => self::NEW_NAME]);
    }

    public function down(): void
    {
        $this->db->table('site_setting')
            ->where('id', 1)
            ->where('site_name', self::NEW_NAME)
            ->update(['site_name' => self::OLD_NAME]);
    }
}
