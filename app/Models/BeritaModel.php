<?php

declare(strict_types=1);

namespace App\Models;

use App\Entities\Berita;
use CodeIgniter\Model;

class BeritaModel extends Model
{
    protected $table = 'berita';

    protected $primaryKey = 'id';

    protected $returnType = Berita::class;

    protected $useSoftDeletes = true;

    protected $useTimestamps = true;

    protected $allowedFields = [
        'judul',
        'slug',
        'kategori',
        'tags',
        'ringkasan',
        'konten',
        'gambar_utama',
        'status',
        'tanggal_terbit',
        'view_count',
    ];
}
