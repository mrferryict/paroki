<?php

declare(strict_types=1);

namespace App\Models;

use App\Entities\JadwalMisa;
use CodeIgniter\Model;

class JadwalMisaModel extends Model
{
    protected $table = 'jadwal_misa';

    protected $primaryKey = 'id';

    protected $returnType = JadwalMisa::class;

    protected $useSoftDeletes = true;

    protected $useTimestamps = false;

    protected $allowedFields = [
        'jenis',
        'hari_label',
        'jam',
        'catatan',
        'urutan',
        'is_active',
    ];
}
