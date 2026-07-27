<?php

declare(strict_types=1);

namespace App\Entities;

use App\Enums\BeritaKategori;
use App\Enums\PublishStatus;
use CodeIgniter\Entity\Entity;

class Berita extends Entity
{
    protected $casts = [
        'id'     => 'integer',
        'status' => PublishStatus::class,
        'kategori' => BeritaKategori::class,
    ];

    protected $dates = ['tanggal_terbit', 'created_at', 'updated_at', 'deleted_at'];
}
