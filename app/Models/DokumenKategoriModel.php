<?php

declare(strict_types=1);

namespace App\Models;

use App\Entities\DokumenKategoriRecord;
use CodeIgniter\Model;

class DokumenKategoriModel extends Model
{
    protected $table = 'dokumen_kategori';

    protected $primaryKey = 'id';

    protected $returnType = DokumenKategoriRecord::class;

    protected $useSoftDeletes = true;

    protected $useTimestamps = true;

    protected $allowedFields = [
        'slug',
        'label',
        'urutan',
        'is_active',
    ];
}
