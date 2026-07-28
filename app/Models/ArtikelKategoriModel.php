<?php

declare(strict_types=1);

namespace App\Models;

use App\Entities\ArtikelKategoriRecord;
use CodeIgniter\Model;

class ArtikelKategoriModel extends Model
{
    protected $table = 'artikel_kategori';

    protected $primaryKey = 'id';

    protected $returnType = ArtikelKategoriRecord::class;

    protected $useSoftDeletes = true;

    protected $useTimestamps = true;

    protected $allowedFields = [
        'slug',
        'label',
        'urutan',
        'is_active',
    ];
}
