<?php

declare(strict_types=1);

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Lingkungan extends Entity
{
    protected $casts = [
        'id'         => 'integer',
        'wilayah_id' => 'integer',
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
}
