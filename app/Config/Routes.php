<?php

declare(strict_types=1);

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

// Berita & Katekese publik — CONTEXT.md §5
$routes->get('berita/(:segment)', 'BeritaController::show/$1', ['as' => 'berita.show']);
$routes->get('berita', 'BeritaController::index', ['as' => 'berita.index']);
$routes->get('katekese/(:segment)/(:segment)', 'KatekeseController::show/$1/$2', ['as' => 'katekese.show']);
$routes->get('katekese/(:segment)', 'KatekeseController::index/$1', ['as' => 'katekese.kategori']);
$routes->get('katekese', 'KatekeseController::index', ['as' => 'katekese.index']);
$routes->get('galeri/(:segment)', 'GaleriController::show/$1', ['as' => 'galeri.show']);
$routes->get('galeri', 'GaleriController::index', ['as' => 'galeri.index']);

// Unduhan publik & download terkontrol — CONTEXT.md §5 / §4.8
$routes->get('unduhan', 'UnduhanController::index', ['as' => 'unduhan.index']);
$routes->get('dokumen/(:num)/unduh', 'DokumenController::download/$1', ['as' => 'dokumen.download']);

// Formulir publik — HTMX partial response (CONTEXT.md §5 / §4.5)
$routes->post('formulir', 'FormulirController::submit', ['as' => 'formulir.submit']);

// Logout outside the session filter — CSRF-exempt (see Config\Filters).
// .cursorrules §4.3 / CONTEXT.md §3
$routes->match(['GET', 'POST'], 'logout', 'ProfileController::logout');

// Shield auth routes (/cp, magic-link, auth-actions). Register & logout handled above.
service('auth')->routes($routes, ['except' => ['register', 'logout']]);

// Admin — CONTEXT.md §3: session auth + grup admin
$routes->group('admin', ['filter' => ['session', 'group:admin']], static function (RouteCollection $routes): void {
    $routes->get('/', static fn () => redirect()->to('/admin/hero-slide'));

    $routes->get('hero-slide', 'Admin\HeroSlideController::index', ['as' => 'admin.hero-slide.index']);
    $routes->get('hero-slide/new', 'Admin\HeroSlideController::new', ['as' => 'admin.hero-slide.new']);
    $routes->post('hero-slide', 'Admin\HeroSlideController::create', ['as' => 'admin.hero-slide.create']);
    $routes->get('hero-slide/(:num)/edit', 'Admin\HeroSlideController::edit/$1', ['as' => 'admin.hero-slide.edit']);
    $routes->post('hero-slide/(:num)', 'Admin\HeroSlideController::update/$1', ['as' => 'admin.hero-slide.update']);
    $routes->post('hero-slide/(:num)/delete', 'Admin\HeroSlideController::delete/$1', ['as' => 'admin.hero-slide.delete']);
    $routes->post('hero-slide/(:num)/move-up', 'Admin\HeroSlideController::moveUp/$1', ['as' => 'admin.hero-slide.move-up']);
    $routes->post('hero-slide/(:num)/move-down', 'Admin\HeroSlideController::moveDown/$1', ['as' => 'admin.hero-slide.move-down']);

    $routes->get('pengaturan', 'Admin\PengaturanController::index', ['as' => 'admin.pengaturan.index']);
    $routes->post('pengaturan/logo', 'Admin\PengaturanController::updateLogo', ['as' => 'admin.pengaturan.logo']);
    $routes->post('pengaturan/logo/hapus', 'Admin\PengaturanController::removeLogo', ['as' => 'admin.pengaturan.logo.remove']);

    $routes->get('dewan-paroki', 'Admin\DewanParokiBidangController::index', ['as' => 'admin.dewan-paroki.index']);
    $routes->get('dewan-paroki/new', 'Admin\DewanParokiBidangController::new', ['as' => 'admin.dewan-paroki.new']);
    $routes->post('dewan-paroki', 'Admin\DewanParokiBidangController::create', ['as' => 'admin.dewan-paroki.create']);
    $routes->get('dewan-paroki/(:num)/edit', 'Admin\DewanParokiBidangController::edit/$1', ['as' => 'admin.dewan-paroki.edit']);
    $routes->post('dewan-paroki/(:num)', 'Admin\DewanParokiBidangController::update/$1', ['as' => 'admin.dewan-paroki.update']);
    $routes->post('dewan-paroki/(:num)/delete', 'Admin\DewanParokiBidangController::delete/$1', ['as' => 'admin.dewan-paroki.delete']);
    $routes->post('dewan-paroki/(:num)/move-up', 'Admin\DewanParokiBidangController::moveUp/$1', ['as' => 'admin.dewan-paroki.move-up']);
    $routes->post('dewan-paroki/(:num)/move-down', 'Admin\DewanParokiBidangController::moveDown/$1', ['as' => 'admin.dewan-paroki.move-down']);
    $routes->get('dewan-paroki/(:num)/penjabat/new', 'Admin\DewanParokiPenjabatController::new/$1', ['as' => 'admin.dewan-paroki.penjabat.new']);
    $routes->post('dewan-paroki/(:num)/penjabat', 'Admin\DewanParokiPenjabatController::create/$1', ['as' => 'admin.dewan-paroki.penjabat.create']);
    $routes->get('dewan-paroki/(:num)/penjabat/(:num)/edit', 'Admin\DewanParokiPenjabatController::edit/$1/$2', ['as' => 'admin.dewan-paroki.penjabat.edit']);
    $routes->post('dewan-paroki/(:num)/penjabat/(:num)', 'Admin\DewanParokiPenjabatController::update/$1/$2', ['as' => 'admin.dewan-paroki.penjabat.update']);
    $routes->post('dewan-paroki/(:num)/penjabat/(:num)/delete', 'Admin\DewanParokiPenjabatController::delete/$1/$2', ['as' => 'admin.dewan-paroki.penjabat.delete']);

    $routes->get('sakramen-jenis', 'Admin\SakramenJenisController::index', ['as' => 'admin.sakramen-jenis.index']);
    $routes->get('sakramen-jenis/new', 'Admin\SakramenJenisController::new', ['as' => 'admin.sakramen-jenis.new']);
    $routes->post('sakramen-jenis', 'Admin\SakramenJenisController::create', ['as' => 'admin.sakramen-jenis.create']);
    $routes->get('sakramen-jenis/(:num)/edit', 'Admin\SakramenJenisController::edit/$1', ['as' => 'admin.sakramen-jenis.edit']);
    $routes->post('sakramen-jenis/(:num)', 'Admin\SakramenJenisController::update/$1', ['as' => 'admin.sakramen-jenis.update']);
    $routes->post('sakramen-jenis/(:num)/delete', 'Admin\SakramenJenisController::delete/$1', ['as' => 'admin.sakramen-jenis.delete']);
    $routes->post('sakramen-jenis/(:num)/move-up', 'Admin\SakramenJenisController::moveUp/$1', ['as' => 'admin.sakramen-jenis.move-up']);
    $routes->post('sakramen-jenis/(:num)/move-down', 'Admin\SakramenJenisController::moveDown/$1', ['as' => 'admin.sakramen-jenis.move-down']);

    $routes->get('jadwal-misa', 'Admin\JadwalMisaController::index', ['as' => 'admin.jadwal-misa.index']);
    $routes->get('jadwal-misa/new', 'Admin\JadwalMisaController::new', ['as' => 'admin.jadwal-misa.new']);
    $routes->post('jadwal-misa', 'Admin\JadwalMisaController::create', ['as' => 'admin.jadwal-misa.create']);
    $routes->get('jadwal-misa/(:num)/edit', 'Admin\JadwalMisaController::edit/$1', ['as' => 'admin.jadwal-misa.edit']);
    $routes->post('jadwal-misa/(:num)', 'Admin\JadwalMisaController::update/$1', ['as' => 'admin.jadwal-misa.update']);
    $routes->post('jadwal-misa/(:num)/delete', 'Admin\JadwalMisaController::delete/$1', ['as' => 'admin.jadwal-misa.delete']);
    $routes->post('jadwal-misa/(:num)/move-up', 'Admin\JadwalMisaController::moveUp/$1', ['as' => 'admin.jadwal-misa.move-up']);
    $routes->post('jadwal-misa/(:num)/move-down', 'Admin\JadwalMisaController::moveDown/$1', ['as' => 'admin.jadwal-misa.move-down']);

    $routes->get('wilayah', 'Admin\WilayahController::index', ['as' => 'admin.wilayah.index']);
    $routes->get('wilayah/new', 'Admin\WilayahController::new', ['as' => 'admin.wilayah.new']);
    $routes->post('wilayah', 'Admin\WilayahController::create', ['as' => 'admin.wilayah.create']);
    $routes->get('wilayah/(:num)', 'Admin\WilayahController::show/$1', ['as' => 'admin.wilayah.show']);
    $routes->get('wilayah/(:num)/edit', 'Admin\WilayahController::edit/$1', ['as' => 'admin.wilayah.edit']);
    $routes->post('wilayah/(:num)', 'Admin\WilayahController::update/$1', ['as' => 'admin.wilayah.update']);
    $routes->post('wilayah/(:num)/delete', 'Admin\WilayahController::delete/$1', ['as' => 'admin.wilayah.delete']);

    $routes->get('wilayah/(:num)/lingkungan', 'Admin\LingkunganController::index/$1', ['as' => 'admin.lingkungan.index']);
    $routes->get('wilayah/(:num)/lingkungan/new', 'Admin\LingkunganController::new/$1', ['as' => 'admin.lingkungan.new']);
    $routes->post('wilayah/(:num)/lingkungan', 'Admin\LingkunganController::create/$1', ['as' => 'admin.lingkungan.create']);
    $routes->get('wilayah/(:num)/lingkungan/(:num)', 'Admin\LingkunganController::show/$1/$2', ['as' => 'admin.lingkungan.show']);
    $routes->get('wilayah/(:num)/lingkungan/(:num)/edit', 'Admin\LingkunganController::edit/$1/$2', ['as' => 'admin.lingkungan.edit']);
    $routes->post('wilayah/(:num)/lingkungan/(:num)', 'Admin\LingkunganController::update/$1/$2', ['as' => 'admin.lingkungan.update']);
    $routes->post('wilayah/(:num)/lingkungan/(:num)/delete', 'Admin\LingkunganController::delete/$1/$2', ['as' => 'admin.lingkungan.delete']);

    $routes->get('berita', 'Admin\BeritaController::index', ['as' => 'admin.berita.index']);
    $routes->get('berita/new', 'Admin\BeritaController::new', ['as' => 'admin.berita.new']);
    $routes->post('berita', 'Admin\BeritaController::create', ['as' => 'admin.berita.create']);
    $routes->get('berita/(:num)/edit', 'Admin\BeritaController::edit/$1', ['as' => 'admin.berita.edit']);
    $routes->post('berita/(:num)', 'Admin\BeritaController::update/$1', ['as' => 'admin.berita.update']);
    $routes->post('berita/(:num)/delete', 'Admin\BeritaController::delete/$1', ['as' => 'admin.berita.delete']);

    $routes->get('artikel/new', 'Admin\ArtikelController::new', ['as' => 'admin.artikel.new']);
    $routes->get('artikel/kategori/(:segment)/new', 'Admin\ArtikelController::new/$1', ['as' => 'admin.artikel.new.kategori']);
    $routes->get('artikel/kategori/(:segment)', 'Admin\ArtikelController::index/$1', ['as' => 'admin.artikel.kategori']);
    $routes->get('artikel', 'Admin\ArtikelController::index', ['as' => 'admin.artikel.index']);
    $routes->post('artikel', 'Admin\ArtikelController::create', ['as' => 'admin.artikel.create']);
    $routes->get('artikel/(:num)/edit', 'Admin\ArtikelController::edit/$1', ['as' => 'admin.artikel.edit']);
    $routes->post('artikel/(:num)', 'Admin\ArtikelController::update/$1', ['as' => 'admin.artikel.update']);
    $routes->post('artikel/(:num)/delete', 'Admin\ArtikelController::delete/$1', ['as' => 'admin.artikel.delete']);

    $routes->get('galeri', 'Admin\GaleriController::index', ['as' => 'admin.galeri.index']);
    $routes->get('galeri/event/new', 'Admin\GaleriController::newEvent', ['as' => 'admin.galeri.event.new']);
    $routes->post('galeri/event', 'Admin\GaleriController::createEvent', ['as' => 'admin.galeri.event.create']);
    $routes->get('galeri/event/(:num)/edit', 'Admin\GaleriController::editEvent/$1', ['as' => 'admin.galeri.event.edit']);
    $routes->post('galeri/event/(:num)', 'Admin\GaleriController::updateEvent/$1', ['as' => 'admin.galeri.event.update']);
    $routes->post('galeri/event/(:num)/delete', 'Admin\GaleriController::deleteEvent/$1', ['as' => 'admin.galeri.event.delete']);
    $routes->post('galeri/event/(:num)/move-up', 'Admin\GaleriController::moveEventUp/$1', ['as' => 'admin.galeri.event.move-up']);
    $routes->post('galeri/event/(:num)/move-down', 'Admin\GaleriController::moveEventDown/$1', ['as' => 'admin.galeri.event.move-down']);
    $routes->get('galeri/(:num)/item/new', 'Admin\GaleriController::newItem/$1', ['as' => 'admin.galeri.item.new']);
    $routes->post('galeri/(:num)/item', 'Admin\GaleriController::createItem/$1', ['as' => 'admin.galeri.item.create']);
    $routes->get('galeri/(:num)/item/(:num)/edit', 'Admin\GaleriController::editItem/$1/$2', ['as' => 'admin.galeri.item.edit']);
    $routes->post('galeri/(:num)/item/(:num)', 'Admin\GaleriController::updateItem/$1/$2', ['as' => 'admin.galeri.item.update']);
    $routes->post('galeri/(:num)/item/(:num)/delete', 'Admin\GaleriController::deleteItem/$1/$2', ['as' => 'admin.galeri.item.delete']);
    $routes->post('galeri/(:num)/item/(:num)/move-up', 'Admin\GaleriController::moveItemUp/$1/$2', ['as' => 'admin.galeri.item.move-up']);
    $routes->post('galeri/(:num)/item/(:num)/move-down', 'Admin\GaleriController::moveItemDown/$1/$2', ['as' => 'admin.galeri.item.move-down']);

    $routes->get('katekese-kategori', 'Admin\KatekeseKategoriController::index', ['as' => 'admin.katekese-kategori.index']);
    $routes->get('katekese-kategori/new', 'Admin\KatekeseKategoriController::new', ['as' => 'admin.katekese-kategori.new']);
    $routes->post('katekese-kategori', 'Admin\KatekeseKategoriController::create', ['as' => 'admin.katekese-kategori.create']);
    $routes->get('katekese-kategori/(:num)/edit', 'Admin\KatekeseKategoriController::edit/$1', ['as' => 'admin.katekese-kategori.edit']);
    $routes->post('katekese-kategori/(:num)', 'Admin\KatekeseKategoriController::update/$1', ['as' => 'admin.katekese-kategori.update']);
    $routes->post('katekese-kategori/(:num)/delete', 'Admin\KatekeseKategoriController::delete/$1', ['as' => 'admin.katekese-kategori.delete']);

    $routes->get('unduhan-kategori', 'Admin\UnduhanKategoriController::index', ['as' => 'admin.unduhan-kategori.index']);
    $routes->get('unduhan-kategori/new', 'Admin\UnduhanKategoriController::new', ['as' => 'admin.unduhan-kategori.new']);
    $routes->post('unduhan-kategori', 'Admin\UnduhanKategoriController::create', ['as' => 'admin.unduhan-kategori.create']);
    $routes->get('unduhan-kategori/(:num)/edit', 'Admin\UnduhanKategoriController::edit/$1', ['as' => 'admin.unduhan-kategori.edit']);
    $routes->post('unduhan-kategori/(:num)', 'Admin\UnduhanKategoriController::update/$1', ['as' => 'admin.unduhan-kategori.update']);
    $routes->post('unduhan-kategori/(:num)/delete', 'Admin\UnduhanKategoriController::delete/$1', ['as' => 'admin.unduhan-kategori.delete']);

    $routes->get('dokumen', 'Admin\DokumenController::index', ['as' => 'admin.dokumen.index']);
    $routes->get('dokumen/new', 'Admin\DokumenController::new', ['as' => 'admin.dokumen.new']);
    $routes->post('dokumen', 'Admin\DokumenController::create', ['as' => 'admin.dokumen.create']);
    $routes->get('dokumen/(:num)/edit', 'Admin\DokumenController::edit/$1', ['as' => 'admin.dokumen.edit']);
    $routes->post('dokumen/(:num)', 'Admin\DokumenController::update/$1', ['as' => 'admin.dokumen.update']);
    $routes->post('dokumen/(:num)/delete', 'Admin\DokumenController::delete/$1', ['as' => 'admin.dokumen.delete']);

    $routes->get('pendaftaran', 'Admin\PendaftaranController::index', ['as' => 'admin.pendaftaran.index']);
    $routes->get('pendaftaran/(:num)', 'Admin\PendaftaranController::show/$1', ['as' => 'admin.pendaftaran.show']);
    $routes->post('pendaftaran/(:num)/status', 'Admin\PendaftaranController::updateStatus/$1', ['as' => 'admin.pendaftaran.update-status']);
});
