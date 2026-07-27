<?php

declare(strict_types=1);

namespace Config;

use App\Models\DewanParokiBidangModel;
use App\Models\HeroSlideModel;
use App\Models\JadwalMisaModel;
use App\Models\SakramenJenisModel;
use App\Services\DewanParokiBidangService;
use App\Services\HeroSlideService;
use App\Services\JadwalMisaService;
use App\Services\SakramenJenisService;
use CodeIgniter\Config\BaseService;

class Services extends BaseService
{
    public static function heroSlideService(bool $getShared = true): HeroSlideService
    {
        if ($getShared) {
            return static::getSharedInstance('heroSlideService');
        }

        return new HeroSlideService(new HeroSlideModel());
    }

    public static function dewanParokiBidangService(bool $getShared = true): DewanParokiBidangService
    {
        if ($getShared) {
            return static::getSharedInstance('dewanParokiBidangService');
        }

        return new DewanParokiBidangService(new DewanParokiBidangModel());
    }

    public static function sakramenJenisService(bool $getShared = true): SakramenJenisService
    {
        if ($getShared) {
            return static::getSharedInstance('sakramenJenisService');
        }

        return new SakramenJenisService(new SakramenJenisModel());
    }

    public static function jadwalMisaService(bool $getShared = true): JadwalMisaService
    {
        if ($getShared) {
            return static::getSharedInstance('jadwalMisaService');
        }

        return new JadwalMisaService(new JadwalMisaModel());
    }
}
