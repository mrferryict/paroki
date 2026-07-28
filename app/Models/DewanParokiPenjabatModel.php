<?php

declare(strict_types=1);

namespace App\Models;

use App\Entities\DewanParokiPenjabat;
use CodeIgniter\Model;

class DewanParokiPenjabatModel extends Model
{
    protected $table = 'dewan_paroki_penjabat';

    protected $primaryKey = 'id';

    protected $returnType = DewanParokiPenjabat::class;

    protected $useSoftDeletes = true;

    protected $useTimestamps = true;

    protected $allowedFields = [
        'bidang_id',
        'nama',
        'whatsapp_cipher',
        'whatsapp_hash',
        'urutan',
    ];
}
