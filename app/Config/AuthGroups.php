<?php

declare(strict_types=1);

namespace Config;

use CodeIgniter\Shield\Config\AuthGroups as ShieldAuthGroups;

class AuthGroups extends ShieldAuthGroups
{
    /**
     * Grup default untuk user baru (shield:user create tanpa -g).
     */
    public string $defaultGroup = 'superadmin';

    /**
     * @var array<string, array<string, string>>
     */
    public array $groups = [
        'superadmin' => [
            'title'       => 'Super Admin',
            'description' => 'Pemilik situs — akses penuh termasuk pengaturan situs.',
        ],
        'editor' => [
            'title'       => 'Editor',
            'description' => 'Membantu mengelola konten; tidak dapat mengubah pengaturan situs.',
        ],
    ];

    /**
     * @var array<string, string>
     */
    public array $permissions = [
        'admin.access'   => 'Can access the parish admin area',
        'admin.settings' => 'Can manage site settings (logo, name, copyright)',
    ];

    /**
     * @var array<string, list<string>>
     */
    public array $matrix = [
        'superadmin' => [
            'admin.*',
        ],
        'editor' => [
            'admin.access',
        ],
    ];
}
