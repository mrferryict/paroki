<?php

declare(strict_types=1);

namespace App\Models;

use App\Entities\Pendaftaran;
use CodeIgniter\Model;

class PendaftaranModel extends Model
{
    protected $table = 'pendaftaran';

    protected $primaryKey = 'id';

    protected $returnType = Pendaftaran::class;

    protected $useSoftDeletes = false;

    protected $useTimestamps = true;

    protected $allowedFields = [
        'nama_lengkap',
        'whatsapp_cipher',
        'whatsapp_hash',
        'sakramen_jenis_id',
        'pesan',
        'status',
    ];
}
