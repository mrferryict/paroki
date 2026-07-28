<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<section class="py-12 lg:py-16">
    <div class="mx-auto max-w-6xl px-4 sm:px-6">
        <nav class="text-sm text-stone-500">
            <a href="<?= site_url('/') ?>" class="hover:text-maroon transition-colors">Beranda</a>
            <span class="mx-2">/</span>
            <a href="<?= site_url('galeri') ?>" class="hover:text-maroon transition-colors">Galeri</a>
            <span class="mx-2">/</span>
            <span class="text-maroon"><?= esc($event['judul']) ?></span>
        </nav>

        <div class="mt-6 flex flex-wrap items-end justify-between gap-4">
            <div>
                <span class="inline-block h-1 w-12 bg-gold"></span>
                <h1 class="mt-4 font-display text-3xl font-semibold text-maroon lg:text-4xl"><?= esc($event['judul']) ?></h1>
            </div>
            <a href="<?= site_url('galeri') ?>"
               class="text-sm font-semibold text-maroon hover:text-gold transition-colors">
                ← Semua galeri
            </a>
        </div>

        <?= view('galeri/partials/event_media', ['event' => $event]) ?>
    </div>
</section>
<?= $this->endSection() ?>
