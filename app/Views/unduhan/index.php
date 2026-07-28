<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<section class="py-12 lg:py-16">
    <div class="mx-auto max-w-6xl px-4 sm:px-6">
        <nav class="text-sm text-stone-500">
            <a href="<?= site_url('/') ?>" class="hover:text-maroon transition-colors">Beranda</a>
            <span class="mx-2">/</span>
            <span class="text-maroon">Unduhan</span>
        </nav>

        <div class="mt-6">
            <span class="inline-block h-1 w-12 bg-gold"></span>
            <h1 class="mt-4 font-display text-3xl font-semibold text-maroon lg:text-4xl">Unduhan</h1>
            <p class="mt-3 max-w-2xl text-stone-600">
                Formulir, warta paroki, majalah, dan dokumen resmi paroki.
            </p>
        </div>

        <div class="mt-8 flex flex-wrap gap-2">
            <?php
            $allUrl      = site_url('unduhan');
            $isAllActive = ($activeKategori ?? '') === '';
            ?>
            <a href="<?= esc($allUrl) ?>"
               class="rounded-full px-4 py-2 text-sm font-medium transition <?= $isAllActive ? 'bg-maroon text-ivory' : 'bg-white text-stone-600 ring-1 ring-gold/30 hover:bg-maroon/5' ?>">
                Semua
            </a>
            <?php foreach ($kategoriOptions as $value => $label): ?>
                <?php $isActive = ($activeKategori ?? '') === $value; ?>
                <a href="<?= esc(site_url('unduhan?kategori=' . urlencode($value))) ?>"
                   class="rounded-full px-4 py-2 text-sm font-medium transition <?= $isActive ? 'bg-maroon text-ivory' : 'bg-white text-stone-600 ring-1 ring-gold/30 hover:bg-maroon/5' ?>">
                    <?= esc($label) ?>
                </a>
            <?php endforeach ?>
        </div>

        <?php if ($items === []): ?>
            <div class="mt-12 rounded-2xl border border-gold/20 bg-white px-6 py-12 text-center">
                <p class="text-stone-500">Belum ada unduhan<?= ($activeKategori ?? '') !== '' ? ' untuk kategori ini' : '' ?>.</p>
            </div>
        <?php else: ?>
            <ul class="mt-10 space-y-3">
                <?php foreach ($items as $item): ?>
                    <li>
                        <a href="<?= esc($item['downloadUrl']) ?>"
                           class="flex items-center gap-4 rounded-xl border border-gold/20 bg-white px-5 py-4 transition hover:border-maroon/30 hover:bg-maroon/5">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-maroon/10 text-maroon">
                                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 3v12M8 11l4 4 4-4M4 21h16"/>
                                </svg>
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="block font-medium text-stone-800"><?= esc($item['nama']) ?></span>
                                <span class="mt-0.5 block text-xs text-stone-500"><?= esc($item['kategoriLabel']) ?></span>
                            </span>
                            <span class="hidden text-sm font-semibold text-maroon sm:inline">Unduh</span>
                        </a>
                    </li>
                <?php endforeach ?>
            </ul>
        <?php endif ?>
    </div>
</section>
<?= $this->endSection() ?>
