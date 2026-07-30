<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MigrateAdminGroupToSuperadmin extends Migration
{
    public function up(): void
    {
        $this->db->table('auth_groups_users')
            ->where('group', 'admin')
            ->update(['group' => 'superadmin']);
    }

    public function down(): void
    {
        $this->db->table('auth_groups_users')
            ->where('group', 'superadmin')
            ->update(['group' => 'admin']);
    }
}
