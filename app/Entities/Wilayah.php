<?php

declare(strict_types=1);

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Wilayah extends Entity
{
    protected $casts = [
        'id' => 'integer',
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
}
