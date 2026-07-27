<?php

declare(strict_types=1);

/**
 * This file is part of CodeIgniter Shield.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Config;

use CodeIgniter\Shield\Config\AuthGroups as ShieldAuthGroups;

class AuthGroups extends ShieldAuthGroups
{
    /**
     * --------------------------------------------------------------------
     * Default Group
     * --------------------------------------------------------------------
     * The group that a newly registered user is added to.
     * CONTEXT.md §3 — satu grup saja: admin.
     */
    public string $defaultGroup = 'admin';

    /**
     * --------------------------------------------------------------------
     * Groups
     * --------------------------------------------------------------------
     * CONTEXT.md §3 / §2 — jangan buat grup lain sebelum dibutuhkan.
     *
     * @var array<string, array<string, string>>
     */
    public array $groups = [
        'admin' => [
            'title'       => 'Admin',
            'description' => 'Akses penuh ke seluruh panel admin paroki.',
        ],
    ];

    /**
     * --------------------------------------------------------------------
     * Permissions
     * --------------------------------------------------------------------
     *
     * @var array<string, string>
     */
    public array $permissions = [
        'admin.access' => 'Can access the parish admin area',
    ];

    /**
     * --------------------------------------------------------------------
     * Permissions Matrix
     * --------------------------------------------------------------------
     *
     * @var array<string, list<string>>
     */
    public array $matrix = [
        'admin' => [
            'admin.*',
        ],
    ];
}
