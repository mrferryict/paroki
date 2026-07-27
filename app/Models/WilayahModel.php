<?php

declare(strict_types=1);

namespace App\Models;

use App\Entities\Wilayah;
use CodeIgniter\Model;

class WilayahModel extends Model
{
    protected $table = 'wilayah';

    protected $primaryKey = 'id';

    protected $returnType = Wilayah::class;

    protected $useSoftDeletes = true;

    protected $useTimestamps = true;

    protected $allowedFields = [
        'nama',
        'ketua_nama',
        'ketua_kontak_cipher',
        'ketua_kontak_hash',
    ];
}
