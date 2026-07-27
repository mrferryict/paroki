<?php /** @var array<string, mixed> $detail */ ?>
<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<section class="py-12 lg:py-16">
    <div class="mx-auto max-w-3xl px-4 sm:px-6">
        <nav class="text-sm text-stone-500">
            <a href="<?= site_url('/') ?>" class="hover:text-maroon transition-colors">Beranda</a>
            <span class="mx-2">/</span>
            <a href="<?= site_url('katekese') ?>" class="hover:text-maroon transition-colors">Katekese</a>
            <span class="mx-2">/</span>
            <a href="<?= esc(site_url('katekese/' . $detail['kategori'])) ?>" class="hover:text-maroon transition-colors">
                <?= esc($detail['kategoriLabel']) ?>
            </a>
        </nav>

        <article class="mt-8">
            <div class="flex flex-wrap items-center gap-2 text-sm">
                <span class="rounded-full bg-gold/20 px-3 py-1 font-medium text-maroon">
                    <?= esc($detail['kategoriLabel']) ?>
                </span>
                <?php if ($detail['tanggalTerbit'] !== ''): ?>
                    <time class="text-stone-500"><?= esc($detail['tanggalTerbit']) ?></time>
                <?php endif ?>
            </div>

            <h1 class="mt-4 font-display text-3xl font-semibold text-maroon lg:text-4xl">
                <?= esc($detail['judul']) ?>
            </h1>

            <?php if ($detail['konten'] !== ''): ?>
                <div class="prose prose-stone mt-8 max-w-none text-base leading-relaxed text-stone-700 whitespace-pre-line">
                    <?= esc($detail['konten']) ?>
                </div>
            <?php endif ?>
        </article>

        <div class="mt-12 flex flex-wrap gap-6 border-t border-gold/20 pt-8">
            <a href="<?= esc(site_url('katekese/' . $detail['kategori'])) ?>"
               class="inline-flex items-center gap-2 text-sm font-semibold text-maroon hover:text-gold transition-colors">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Kembali ke <?= esc($detail['kategoriLabel']) ?>
            </a>
            <a href="<?= site_url('katekese') ?>"
               class="text-sm font-semibold text-stone-500 hover:text-maroon transition-colors">
                Semua katekese
            </a>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
