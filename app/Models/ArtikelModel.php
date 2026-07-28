<?php

declare(strict_types=1);

namespace App\Models;

use App\Entities\Artikel;
use CodeIgniter\Model;

class ArtikelModel extends Model
{
    protected $table = 'artikel';

    protected $primaryKey = 'id';

    protected $returnType = Artikel::class;

    protected $useSoftDeletes = true;

    protected $useTimestamps = true;

    protected $allowedFields = [
        'judul',
        'slug',
        'kategori',
        'konten',
        'status',
        'tanggal_terbit',
        'view_count',
    ];
}
