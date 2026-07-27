<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<section class="py-12 lg:py-16">
    <div class="mx-auto max-w-6xl px-4 sm:px-6">
        <nav class="text-sm text-stone-500">
            <a href="<?= site_url('/') ?>" class="hover:text-maroon transition-colors">Beranda</a>
            <span class="mx-2">/</span>
            <span class="text-maroon">Katekese & Renungan</span>
        </nav>

        <div class="mt-6 flex flex-wrap items-end justify-between gap-4">
            <div>
                <span class="inline-block h-1 w-12 bg-gold"></span>
                <h1 class="mt-4 font-display text-3xl font-semibold text-maroon lg:text-4xl">Katekese & Renungan</h1>
                <p class="mt-3 text-stone-600">Artikel iman, renungan harian, orang kudus, dan mutiara biblika.</p>
            </div>
            <a href="<?= site_url('/') ?>#katekese"
               class="text-sm font-semibold text-maroon hover:text-gold transition-colors">
                ← Kembali ke beranda
            </a>
        </div>

        <div class="mt-8 flex flex-wrap gap-2">
            <?php $isAllActive = ($activeKategori ?? '') === ''; ?>
            <a href="<?= esc(site_url('katekese')) ?>"
               class="rounded-full px-4 py-2 text-sm font-medium transition <?= $isAllActive ? 'bg-maroon text-ivory' : 'bg-white text-stone-600 ring-1 ring-gold/30 hover:bg-maroon/5' ?>">
                Semua
            </a>
            <?php foreach ($kategoriOptions as $value => $label): ?>
                <?php $isActive = ($activeKategori ?? '') === $value; ?>
                <a href="<?= esc(site_url('katekese/' . $value)) ?>"
                   class="rounded-full px-4 py-2 text-sm font-medium transition <?= $isActive ? 'bg-maroon text-ivory' : 'bg-white text-stone-600 ring-1 ring-gold/30 hover:bg-maroon/5' ?>">
                    <?= esc($label) ?>
                </a>
            <?php endforeach ?>
        </div>

        <?php if ($items === []): ?>
            <div class="mt-12 rounded-2xl border border-gold/20 bg-white px-6 py-12 text-center">
                <p class="text-stone-500">Belum ada artikel terbit<?= ($activeKategori ?? '') !== '' ? ' untuk kategori ini' : '' ?>.</p>
                <p class="mt-2 text-sm text-stone-400">Konten akan muncul otomatis setelah dipublikasikan dari panel admin.</p>
            </div>
        <?php else: ?>
            <div class="mt-10 grid gap-5 sm:grid-cols-2">
                <?php foreach ($items as $item): ?>
                    <article class="rounded-2xl border border-gold/20 bg-white p-6 shadow-sm transition hover:shadow-md">
                        <div class="flex items-center gap-2 text-xs">
                            <span class="rounded-full bg-gold/20 px-2.5 py-1 font-medium text-maroon">
                                <?= esc($item['kategoriLabel']) ?>
                            </span>
                            <span class="text-stone-500"><?= esc($item['tanggalTerbit']) ?></span>
                        </div>
                        <h2 class="mt-3 font-display text-xl font-semibold text-maroon">
                            <a href="<?= esc($item['href']) ?>" class="hover:text-maroon/80">
                                <?= esc($item['judul']) ?>
                            </a>
                        </h2>
                        <?php if ($item['ringkasan'] !== ''): ?>
                            <p class="mt-2 text-sm leading-relaxed text-stone-600">
                                <?= esc($item['ringkasan']) ?>
                            </p>
                        <?php endif ?>
                        <a href="<?= esc($item['href']) ?>"
                           class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-maroon hover:text-gold transition-colors">
                            Baca selengkapnya
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12h14M13 6l6 6-6 6"/>
                            </svg>
                        </a>
                    </article>
                <?php endforeach ?>
            </div>

            <?php
            $katekeseBaseUrl = ($activeKategori ?? '') !== ''
                ? site_url('katekese/' . $activeKategori)
                : site_url('katekese');
            ?>
            <?= view('partials/public_pagination', [
                'pager'             => $pager,
                'queryParams'       => [],
                'paginationBaseUrl' => $katekeseBaseUrl,
                'paginationLabel'   => 'artikel',
            ]) ?>
        <?php endif ?>
    </div>
</section>
<?= $this->endSection() ?>
