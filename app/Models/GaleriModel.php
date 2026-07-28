<?php

declare(strict_types=1);

namespace App\Models;

use App\Entities\Galeri;
use CodeIgniter\Model;

class GaleriModel extends Model
{
    protected $table = 'galeri';

    protected $primaryKey = 'id';

    protected $returnType = Galeri::class;

    protected $useSoftDeletes = true;

    protected $useTimestamps = true;

    protected $allowedFields = [
        'galeri_event_id',
        'jenis',
        'file_path',
        'youtube_url',
        'caption',
        'urutan',
    ];
}
