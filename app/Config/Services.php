<?php

declare(strict_types=1);

namespace Config;

use App\Libraries\PiiCipher;
use App\Models\DewanParokiBidangModel;
use App\Models\HeroSlideModel;
use App\Models\JadwalMisaModel;
use App\Models\LingkunganModel;
use App\Models\SakramenJenisModel;
use App\Models\WilayahModel;
use App\Repositories\LingkunganRepository;
use App\Repositories\WilayahRepository;
use App\Services\DewanParokiBidangService;
use App\Services\HeroSlideService;
use App\Services\JadwalMisaService;
use App\Services\LingkunganService;
use App\Services\SakramenJenisService;
use App\Services\WilayahService;
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

    public static function piiCipher(bool $getShared = true): PiiCipher
    {
        if ($getShared) {
            return static::getSharedInstance('piiCipher');
        }

        return new PiiCipher();
    }

    public static function wilayahService(bool $getShared = true): WilayahService
    {
        if ($getShared) {
            return static::getSharedInstance('wilayahService');
        }

        return new WilayahService(
            new WilayahRepository(new WilayahModel()),
            static::piiCipher(false),
        );
    }

    public static function lingkunganService(bool $getShared = true): LingkunganService
    {
        if ($getShared) {
            return static::getSharedInstance('lingkunganService');
        }

        return new LingkunganService(
            new LingkunganRepository(new LingkunganModel()),
            new WilayahRepository(new WilayahModel()),
            static::piiCipher(false),
        );
    }
}
