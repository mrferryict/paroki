<?php

declare(strict_types=1);

namespace App\Models;

use App\Entities\GaleriEvent;
use CodeIgniter\Model;

class GaleriEventModel extends Model
{
    protected $table = 'galeri_event';

    protected $primaryKey = 'id';

    protected $returnType = GaleriEvent::class;

    protected $useSoftDeletes = true;

    protected $useTimestamps = true;

    protected $allowedFields = [
        'slug',
        'urutan',
        'view_count',
    ];
}
