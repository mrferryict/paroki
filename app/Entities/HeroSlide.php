<?php

declare(strict_types=1);

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class HeroSlide extends Entity
{
    protected $casts = [
        'id'        => 'integer',
        'urutan'    => 'integer',
        'is_active' => 'boolean',
    ];
}
