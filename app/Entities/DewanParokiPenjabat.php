<?php

declare(strict_types=1);

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class DewanParokiPenjabat extends Entity
{
    protected $casts = [
        'id'        => 'integer',
        'bidang_id' => 'integer',
        'urutan'    => 'integer',
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
}
