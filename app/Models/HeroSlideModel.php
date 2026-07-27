<?php

declare(strict_types=1);

namespace App\Models;

use App\Entities\HeroSlide;
use CodeIgniter\Model;

class HeroSlideModel extends Model
{
    protected $table = 'hero_slide';

    protected $primaryKey = 'id';

    protected $returnType = HeroSlide::class;

    protected $useSoftDeletes = false;

    protected $useTimestamps = false;

    protected $allowedFields = [
        'eyebrow',
        'judul',
        'subjudul',
        'cta1_label',
        'cta1_href',
        'cta2_label',
        'cta2_href',
        'gambar',
        'urutan',
        'is_active',
    ];
}
