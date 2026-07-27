<?php

declare(strict_types=1);

/**
 * Sidebar navigasi admin — CONTEXT.md §5 admin modules.
 *
 * @var string $uri
 */
$navItems = [
    ['label' => 'Wilayah & Lingkungan', 'href' => 'admin/wilayah', 'match' => 'admin/wilayah'],
    ['label' => 'Dewan Paroki (DPH)', 'href' => 'admin/dewan-paroki', 'match' => 'admin/dewan-paroki'],
    ['label' => 'Sakramen & Layanan', 'href' => 'admin/sakramen-jenis', 'match' => 'admin/sakramen-jenis'],
    ['label' => 'Pendaftaran', 'href' => 'admin/pendaftaran', 'match' => 'admin/pendaftaran'],
    ['label' => 'Berita & Kegiatan', 'href' => 'admin/berita', 'match' => 'admin/berita'],
    ['label' => 'Katekese & Renungan', 'href' => 'admin/artikel', 'match' => 'admin/artikel'],
    ['label' => 'Dokumen', 'href' => 'admin/dokumen', 'match' => 'admin/dokumen'],
    ['label' => 'Jadwal Misa', 'href' => 'admin/jadwal-misa', 'match' => 'admin/jadwal-misa'],
    ['label' => 'Galeri', 'href' => 'admin/galeri', 'match' => 'admin/galeri'],
    ['label' => 'Hero Slide', 'href' => 'admin/hero-slide', 'match' => 'admin/hero-slide'],
];

$isActive = static function (string $match) use ($uri): bool {
    if ($uri === $match) {
        return true;
    }

    return str_starts_with($uri, $match . '/');
};
?>
<nav class="rounded-lg border border-gold/20 bg-white p-3 shadow-sm">
    <p class="mb-2 px-2 text-xs font-semibold uppercase tracking-wide text-stone-500">Modul Admin</p>
    <ul class="space-y-0.5">
        <?php foreach ($navItems as $item): ?>
            <?php $active = $isActive($item['match']); ?>
            <li>
                <a href="<?= site_url($item['href']) ?>"
                   class="block rounded px-3 py-2 text-sm font-medium transition-colors <?= $active ? 'bg-maroon/10 text-maroon' : 'text-stone-700 hover:bg-maroon/5 hover:text-maroon' ?>">
                    <?= esc($item['label']) ?>
                </a>
            </li>
        <?php endforeach ?>
    </ul>
</nav>
