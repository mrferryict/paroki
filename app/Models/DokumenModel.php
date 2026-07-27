<?php

declare(strict_types=1);

namespace App\Models;

use App\Entities\Dokumen;
use CodeIgniter\Model;

class DokumenModel extends Model
{
    protected $table = 'dokumen';

    protected $primaryKey = 'id';

    protected $returnType = Dokumen::class;

    protected $useSoftDeletes = true;

    protected $useTimestamps = true;

    protected $allowedFields = [
        'nama',
        'file_path',
        'kategori',
    ];
}
