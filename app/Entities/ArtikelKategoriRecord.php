<?php

declare(strict_types=1);

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class ArtikelKategoriRecord extends Entity
{
    protected $casts = [
        'id'        => 'integer',
        'urutan'    => 'integer',
        'is_active' => 'boolean',
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
}
