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

    protected $useSoftDeletes = false;

    protected $useTimestamps = true;

    protected $createdField = 'created_at';

    protected $updatedField = '';

    protected $allowedFields = [
        'file_path',
        'caption',
        'urutan',
    ];
}
