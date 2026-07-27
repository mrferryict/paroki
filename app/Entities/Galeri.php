<?php

declare(strict_types=1);

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Galeri extends Entity
{
    protected $casts = [
        'id'     => 'integer',
        'urutan' => 'integer',
    ];

    protected $dates = ['created_at'];
}
