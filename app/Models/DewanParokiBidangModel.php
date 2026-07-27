<?php

declare(strict_types=1);

namespace App\Models;

use App\Entities\DewanParokiBidang;
use CodeIgniter\Model;

class DewanParokiBidangModel extends Model
{
    protected $table = 'dewan_paroki_bidang';

    protected $primaryKey = 'id';

    protected $returnType = DewanParokiBidang::class;

    protected $useSoftDeletes = true;

    protected $useTimestamps = false;

    protected $allowedFields = [
        'kode',
        'nama_tampilan',
        'deskripsi',
        'icon',
        'urutan',
    ];
}
