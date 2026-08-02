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
<footer class="border-t border-gold/20 bg-stone-900 py-6 text-stone-400 sm:py-7">
    <div class="mx-auto max-w-6xl px-4 sm:px-6">
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4 lg:gap-8">
            <div class="sm:col-span-2">
                <p class="font-display text-lg font-semibold text-ivory sm:text-xl"><?= esc($siteName) ?></p>
                <p class="mt-1 max-w-md text-xs leading-relaxed text-stone-500 sm:text-sm">
                    Persekutuan umat beriman yang bertumbuh dalam doa, pelayanan, dan kebersamaan.
                </p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-gold">Navigasi</p>
                <ul class="mt-2 space-y-1 text-xs sm:text-sm">
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
                <p class="text-xs font-semibold uppercase tracking-wide text-gold">Tautan</p>
                <ul class="mt-2 space-y-1 text-xs sm:text-sm">
                    <li><a href="<?= site_url('berita') ?>" class="hover:text-ivory transition-colors">Arsip Berita</a></li>
                    <li><a href="<?= site_url('katekese') ?>" class="hover:text-ivory transition-colors">Arsip Katekese</a></li>
                    <li><a href="<?= site_url('galeri') ?>" class="hover:text-ivory transition-colors">Galeri</a></li>
                    <li><a href="<?= site_url('unduhan') ?>" class="hover:text-ivory transition-colors">Arsip Unduhan</a></li>
                    <li><a href="<?= url_to('login') ?>" class="hover:text-ivory transition-colors">Login Admin</a></li>
                </ul>
            </div>
        </div>
        <div class="mt-5 border-t border-stone-800 pt-4 text-[11px] sm:text-xs">
            <div class="flex flex-wrap items-center justify-between gap-x-4 gap-y-1">
                <p class="text-stone-400">
                    &copy; <?= esc(date('Y')) ?> <?= esc($siteName) ?>. <?= esc($copyrightText) ?>
                </p>
                <p class="font-mono text-stone-500">
                    PHP <?= esc(PHP_VERSION) ?>
                    · <?= esc((string) $footerMonitorMemoryMb) ?> MB
                    · <?= esc($footerMonitorLoadSeconds) ?> s
                </p>
            </div>
        </div>
    </div>
</footer>
