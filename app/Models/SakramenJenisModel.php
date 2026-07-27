<?php

declare(strict_types=1);

namespace App\Models;

use App\Entities\SakramenJenis;
use CodeIgniter\Model;

class SakramenJenisModel extends Model
{
    protected $table = 'sakramen_jenis';

    protected $primaryKey = 'id';

    protected $returnType = SakramenJenis::class;

    protected $useSoftDeletes = true;

    protected $useTimestamps = false;

    protected $allowedFields = [
        'kode',
        'nama',
        'deskripsi',
        'icon',
        'urutan',
        'is_active',
    ];
}
