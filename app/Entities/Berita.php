<?php

declare(strict_types=1);

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Berita extends Entity
{
    protected $casts = [
        'id' => 'integer',
    ];

    protected $dates = ['tanggal_terbit', 'created_at', 'updated_at', 'deleted_at'];
}
