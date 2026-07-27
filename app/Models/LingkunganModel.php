<?php

declare(strict_types=1);

namespace App\Models;

use App\Entities\Lingkungan;
use CodeIgniter\Model;

class LingkunganModel extends Model
{
    protected $table = 'lingkungan';

    protected $primaryKey = 'id';

    protected $returnType = Lingkungan::class;

    protected $useSoftDeletes = true;

    protected $useTimestamps = true;

    protected $allowedFields = [
        'wilayah_id',
        'nama',
        'ketua_nama',
        'ketua_kontak_cipher',
        'ketua_kontak_hash',
    ];
}
