<?php

declare(strict_types=1);

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

// Logout outside the session filter — CSRF-exempt (see Config\Filters).
// .cursorrules §4.3 / CONTEXT.md §3
$routes->match(['GET', 'POST'], 'logout', 'ProfileController::logout');

// Shield auth routes (login, magic-link, auth-actions). Register & logout handled above.
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

    $routes->get('dewan-paroki', 'Admin\DewanParokiBidangController::index', ['as' => 'admin.dewan-paroki.index']);
    $routes->get('dewan-paroki/new', 'Admin\DewanParokiBidangController::new', ['as' => 'admin.dewan-paroki.new']);
    $routes->post('dewan-paroki', 'Admin\DewanParokiBidangController::create', ['as' => 'admin.dewan-paroki.create']);
    $routes->get('dewan-paroki/(:num)/edit', 'Admin\DewanParokiBidangController::edit/$1', ['as' => 'admin.dewan-paroki.edit']);
    $routes->post('dewan-paroki/(:num)', 'Admin\DewanParokiBidangController::update/$1', ['as' => 'admin.dewan-paroki.update']);
    $routes->post('dewan-paroki/(:num)/delete', 'Admin\DewanParokiBidangController::delete/$1', ['as' => 'admin.dewan-paroki.delete']);
    $routes->post('dewan-paroki/(:num)/move-up', 'Admin\DewanParokiBidangController::moveUp/$1', ['as' => 'admin.dewan-paroki.move-up']);
    $routes->post('dewan-paroki/(:num)/move-down', 'Admin\DewanParokiBidangController::moveDown/$1', ['as' => 'admin.dewan-paroki.move-down']);

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
});
