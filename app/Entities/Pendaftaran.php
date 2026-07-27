<?php

declare(strict_types=1);

namespace App\Entities;

use App\Enums\PendaftaranStatus;
use CodeIgniter\Entity\Entity;

class Pendaftaran extends Entity
{
    protected $casts = [
        'id'                => 'integer',
        'sakramen_jenis_id' => '?integer',
        'status'            => PendaftaranStatus::class,
    ];

    protected $dates = ['created_at', 'updated_at'];
}
