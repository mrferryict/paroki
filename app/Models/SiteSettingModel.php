<?php

declare(strict_types=1);

namespace App\Models;

use App\Entities\SiteSetting;
use CodeIgniter\Model;

class SiteSettingModel extends Model
{
    protected $table = 'site_setting';

    protected $primaryKey = 'id';

    protected $returnType = SiteSetting::class;

    protected $useTimestamps = true;

    protected $allowedFields = [
        'logo_path',
    ];
}
