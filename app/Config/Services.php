<?php

declare(strict_types=1);

namespace Config;

use App\Libraries\PiiCipher;
use App\Libraries\SlugGenerator;
use App\Models\ArtikelModel;
use App\Models\BeritaModel;
use App\Models\DewanParokiBidangModel;
use App\Models\DokumenModel;
use App\Models\GaleriModel;
use App\Models\HeroSlideModel;
use App\Models\JadwalMisaModel;
use App\Models\LingkunganModel;
use App\Models\SakramenJenisModel;
use App\Models\WilayahModel;
use App\Repositories\ArtikelRepository;
use App\Repositories\BeritaRepository;
use App\Repositories\LingkunganRepository;
use App\Repositories\WilayahRepository;
use App\Services\ArtikelService;
use App\Services\BeritaService;
use App\Services\DewanParokiBidangService;
use App\Services\DokumenService;
use App\Services\GaleriService;
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

    public static function slugGenerator(bool $getShared = true): SlugGenerator
    {
        if ($getShared) {
            return static::getSharedInstance('slugGenerator');
        }

        return new SlugGenerator();
    }

    public static function beritaService(bool $getShared = true): BeritaService
    {
        if ($getShared) {
            return static::getSharedInstance('beritaService');
        }

        return new BeritaService(
            new BeritaRepository(new BeritaModel()),
            static::slugGenerator(false),
        );
    }

    public static function artikelService(bool $getShared = true): ArtikelService
    {
        if ($getShared) {
            return static::getSharedInstance('artikelService');
        }

        return new ArtikelService(
            new ArtikelRepository(new ArtikelModel()),
            static::slugGenerator(false),
        );
    }

    public static function galeriService(bool $getShared = true): GaleriService
    {
        if ($getShared) {
            return static::getSharedInstance('galeriService');
        }

        return new GaleriService(new GaleriModel());
    }

    public static function dokumenService(bool $getShared = true): DokumenService
    {
        if ($getShared) {
            return static::getSharedInstance('dokumenService');
        }

        return new DokumenService(new DokumenModel());
    }
}
