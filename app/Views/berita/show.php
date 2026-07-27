<?php /** @var array<string, mixed> $detail */ ?>
<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<section class="py-12 lg:py-16">
    <div class="mx-auto max-w-3xl px-4 sm:px-6">
        <nav class="text-sm text-stone-500">
            <a href="<?= site_url('/') ?>" class="hover:text-maroon transition-colors">Beranda</a>
            <span class="mx-2">/</span>
            <a href="<?= site_url('berita') ?>" class="hover:text-maroon transition-colors">Berita</a>
            <span class="mx-2">/</span>
            <span class="text-maroon line-clamp-1"><?= esc($detail['judul']) ?></span>
        </nav>

        <article class="mt-8">
            <div class="flex flex-wrap items-center gap-2 text-sm">
                <span class="rounded-full bg-maroon/10 px-3 py-1 font-medium text-maroon">
                    <?= esc($detail['kategoriLabel']) ?>
                </span>
                <?php if ($detail['tanggalTerbit'] !== ''): ?>
                    <time class="text-stone-500"><?= esc($detail['tanggalTerbit']) ?></time>
                <?php endif ?>
            </div>

            <h1 class="mt-4 font-display text-3xl font-semibold text-maroon lg:text-4xl">
                <?= esc($detail['judul']) ?>
            </h1>

            <?php if ($detail['gambar'] !== ''): ?>
                <figure class="mt-8 overflow-hidden rounded-2xl border border-gold/20">
                    <img src="<?= esc($detail['gambar']) ?>" alt="<?= esc($detail['judul']) ?>"
                         class="w-full object-cover">
                </figure>
            <?php endif ?>

            <?php if ($detail['ringkasan'] !== ''): ?>
                <p class="mt-8 text-lg leading-relaxed text-stone-600 font-medium">
                    <?= esc($detail['ringkasan']) ?>
                </p>
            <?php endif ?>

            <?php if ($detail['konten'] !== ''): ?>
                <div class="prose prose-stone mt-8 max-w-none text-base leading-relaxed text-stone-700 whitespace-pre-line">
                    <?= esc($detail['konten']) ?>
                </div>
            <?php endif ?>
        </article>

        <div class="mt-12 border-t border-gold/20 pt-8">
            <a href="<?= site_url('berita') ?>"
               class="inline-flex items-center gap-2 text-sm font-semibold text-maroon hover:text-gold transition-colors">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Kembali ke arsip berita
            </a>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
