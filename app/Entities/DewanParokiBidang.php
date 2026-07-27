<?php

declare(strict_types=1);

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class DewanParokiBidang extends Entity
{
    protected $casts = [
        'id'     => 'integer',
        'urutan' => 'integer',
    ];
}
