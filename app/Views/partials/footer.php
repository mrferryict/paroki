<?php
$branding = service('siteSettingService')->getBranding();
$siteName = $branding['siteName'];
$copyrightText = $branding['copyrightText'];

$footerMonitorMemoryMb = round(memory_get_peak_usage(true) / 1024 / 1024, 2);
$footerMonitorLoadSeconds = number_format(
    microtime(true) - (float) ($_SERVER['REQUEST_TIME_FLOAT'] ?? microtime(true)),
    3,
    '.',
    '',
);
?>
<footer class="border-t border-gold/20 bg-stone-900 py-12 text-stone-400">
    <div class="mx-auto max-w-6xl px-4 sm:px-6">
        <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
            <div class="sm:col-span-2">
                <p class="font-display text-2xl font-semibold text-ivory"><?= esc($siteName) ?></p>
                <p class="mt-3 max-w-md text-sm leading-relaxed">
                    Persekutuan umat beriman yang bertumbuh dalam doa, pelayanan, dan kebersamaan.
                </p>
            </div>
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-gold">Navigasi</p>
                <ul class="mt-4 space-y-2 text-sm">
                    <li><a href="<?= site_url('/#profil') ?>" class="hover:text-ivory transition-colors">Profil Paroki</a></li>
                    <li><a href="<?= site_url('/#jadwal') ?>" class="hover:text-ivory transition-colors">Jadwal Misa</a></li>
                    <li><a href="<?= site_url('/#layanan') ?>" class="hover:text-ivory transition-colors">Layanan Paroki</a></li>
                    <li><a href="<?= site_url('berita') ?>" class="hover:text-ivory transition-colors">Berita & Kegiatan</a></li>
                    <li><a href="<?= site_url('katekese') ?>" class="hover:text-ivory transition-colors">Katekese & Renungan</a></li>
                    <li><a href="<?= site_url('galeri') ?>" class="hover:text-ivory transition-colors">Galeri</a></li>
                    <li><a href="<?= site_url('unduhan') ?>" class="hover:text-ivory transition-colors">Unduhan</a></li>
                </ul>
            </div>
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-gold">Tautan</p>
                <ul class="mt-4 space-y-2 text-sm">
                    <li><a href="<?= site_url('berita') ?>" class="hover:text-ivory transition-colors">Arsip Berita</a></li>
                    <li><a href="<?= site_url('katekese') ?>" class="hover:text-ivory transition-colors">Arsip Katekese</a></li>
                    <li><a href="<?= site_url('galeri') ?>" class="hover:text-ivory transition-colors">Galeri</a></li>
                    <li><a href="<?= site_url('unduhan') ?>" class="hover:text-ivory transition-colors">Arsip Unduhan</a></li>
                    <li><a href="<?= url_to('login') ?>" class="hover:text-ivory transition-colors">Login Admin</a></li>
                </ul>
            </div>
        </div>
        <div class="mt-10 border-t border-stone-800 pt-8 text-xs">
            <div class="flex flex-wrap items-center justify-between gap-x-4 gap-y-2">
                <p class="text-left text-stone-400">
                    &copy; <?= esc(date('Y')) ?> <?= esc($siteName) ?>. <?= esc($copyrightText) ?>
                </p>
                <p class="text-right font-mono text-stone-500">
                    PHP <?= esc(PHP_VERSION) ?>
                    · <?= esc((string) $footerMonitorMemoryMb) ?> MB
                    · Loaded in <?= esc($footerMonitorLoadSeconds) ?> s
                </p>
            </div>
        </div>
    </div>
</footer>
