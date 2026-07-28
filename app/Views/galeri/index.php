<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<section class="py-12 lg:py-16">
    <div class="mx-auto max-w-6xl px-4 sm:px-6">
        <nav class="text-sm text-stone-500">
            <a href="<?= site_url('/') ?>" class="hover:text-maroon transition-colors">Beranda</a>
            <span class="mx-2">/</span>
            <span class="text-maroon">Galeri</span>
        </nav>

        <div class="mt-6 flex flex-wrap items-end justify-between gap-4">
            <div>
                <span class="inline-block h-1 w-12 bg-gold"></span>
                <h1 class="mt-4 font-display text-3xl font-semibold text-maroon lg:text-4xl">Galeri Paroki</h1>
                <p class="mt-3 text-stone-600">Dokumentasi foto dan video kegiatan paroki.</p>
            </div>
            <a href="<?= site_url('/') ?>"
               class="text-sm font-semibold text-maroon hover:text-gold transition-colors">
                ← Kembali ke beranda
            </a>
        </div>

        <?php if ($events === []): ?>
            <div class="mt-12 rounded-2xl border border-gold/20 bg-white px-6 py-12 text-center">
                <p class="text-stone-500">Belum ada konten galeri.</p>
            </div>
        <?php else: ?>
            <div class="mt-12 space-y-14">
                <?php foreach ($events as $event): ?>
                    <section>
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <h2 class="font-display text-2xl font-semibold text-maroon">
                                <a href="<?= site_url('galeri/' . ($event['slug'] ?? '')) ?>" class="hover:text-gold transition-colors">
                                    <?= esc($event['judul']) ?>
                                </a>
                            </h2>
                            <a href="<?= site_url('galeri/' . ($event['slug'] ?? '')) ?>"
                               class="text-sm font-semibold text-maroon hover:text-gold transition-colors">Lihat detail →</a>
                        </div>

                        <?= view('galeri/partials/event_media', ['event' => $event]) ?>
                    </section>
                <?php endforeach ?>
            </div>
        <?php endif ?>
    </div>
</section>
<?= $this->endSection() ?>
