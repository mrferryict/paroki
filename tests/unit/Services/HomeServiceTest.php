<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\DTOs\Wilayah\WilayahWithLingkunganDto;
use App\Entities\HeroSlide;
use App\Entities\Wilayah;
use App\Services\ArtikelKategoriService;
use App\Services\ArtikelService;
use App\Services\BeritaService;
use App\Services\DewanParokiBidangService;
use App\Services\HeroSlideService;
use App\Services\HomeService;
use App\Services\JadwalMisaService;
use App\Services\SakramenJenisService;
use App\Services\SiteSettingService;
use App\Services\WilayahService;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class HomeServiceTest extends CIUnitTestCase
{
    public function testGetLandingDataMapsWilayahWithoutPiiColumns(): void
    {
        $wilayah = new Wilayah([
            'id'         => 1,
            'nama'       => 'Wilayah Alpha',
            'ketua_nama' => 'Budi',
        ]);

        $wilayahService = $this->createStub(WilayahService::class);
        $wilayahService->method('findAllWithLingkunganForPublic')->willReturn([
            new WilayahWithLingkunganDto(wilayah: $wilayah, lingkungan: []),
        ]);

        $heroSlideService = $this->createStub(HeroSlideService::class);
        $heroSlideService->method('findAllActiveOrdered')->willReturn([
            new HeroSlide([
                'eyebrow'     => 'Selamat datang',
                'judul'       => 'Paroki',
                'subjudul'    => 'Sub',
                'cta1_label'  => 'A',
                'cta1_href'   => '#a',
                'cta2_label'  => 'B',
                'cta2_href'   => '#b',
                'gambar'      => 'uploads/hero/test.jpg',
                'is_active'   => true,
            ]),
        ]);

        $siteSettingService = $this->createStub(SiteSettingService::class);
        $siteSettingService->method('getBranding')->willReturn([
            'logoUrl'       => null,
            'siteName'      => 'Paroki Santo Mikael Gombong',
            'copyrightText' => 'Semua hak dilindungi.',
        ]);

        $service = new HomeService(
            $heroSlideService,
            $this->createStub(DewanParokiBidangService::class),
            $wilayahService,
            $this->createStub(JadwalMisaService::class),
            $this->createStub(SakramenJenisService::class),
            $this->createStub(BeritaService::class),
            $this->createStub(ArtikelService::class),
            $this->createStub(ArtikelKategoriService::class),
            $siteSettingService,
        );

        $data = $service->getLandingData();

        $this->assertArrayHasKey('wilayahList', $data);
        $this->assertCount(1, $data['wilayahList']);
        $this->assertSame('Wilayah Alpha', $data['wilayahList'][0]['nama']);
        $this->assertArrayNotHasKey('ketua_kontak_cipher', $data['wilayahList'][0]);
        $this->assertArrayNotHasKey('ketua_kontak_hash', $data['wilayahList'][0]);
        $this->assertArrayHasKey('heroSlides', $data);
        $this->assertStringContainsString('uploads/hero/test.jpg', $data['heroSlides'][0]['gambar']);
    }
}
