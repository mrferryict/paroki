<?php /** @var \App\DTOs\Wilayah\WilayahDetailDto $detail */ ?>
<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <div class="flex flex-wrap items-center gap-3">
        <a href="<?= site_url('admin/wilayah') ?>" class="text-sm text-maroon hover:underline">&larr; Kembali ke Wilayah</a>
    </div>

    <div class="rounded-lg border border-gold/20 bg-white p-6 shadow-sm">
        <h1 class="font-display text-3xl font-semibold text-maroon"><?= esc($detail->wilayah->nama) ?></h1>

        <dl class="mt-6 grid gap-4 sm:grid-cols-2">
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-stone-500">Ketua Wilayah</dt>
                <dd class="mt-1 text-base text-stone-800"><?= esc($detail->wilayah->ketua_nama) ?></dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-stone-500">Nomor Kontak</dt>
                <dd class="mt-1 font-mono text-base text-stone-800"><?= esc($detail->ketuaKontak) ?></dd>
            </div>
        </dl>

        <div class="mt-6 flex flex-wrap gap-2">
            <a href="<?= site_url('admin/wilayah/' . $detail->wilayah->id . '/lingkungan') ?>"
               class="rounded bg-maroon px-4 py-2 text-sm font-medium text-ivory hover:bg-maroon/90">Kelola Lingkungan</a>
        </div>
    </div>

    <section class="rounded-lg border border-gold/20 bg-white shadow-sm">
        <div class="border-b border-gold/10 px-4 py-3">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-stone-600">Lingkungan di Wilayah Ini</h2>
        </div>
        <?php if ($detail->lingkungan === []): ?>
            <p class="px-4 py-6 text-sm text-stone-500">Belum ada lingkungan.</p>
        <?php else: ?>
            <ul class="divide-y divide-stone-100">
                <?php foreach ($detail->lingkungan as $lingkungan): ?>
                    <li class="flex items-center justify-between px-4 py-3">
                        <div>
                            <p class="font-medium text-maroon"><?= esc($lingkungan->nama) ?></p>
                            <p class="text-sm text-stone-600">Ketua: <?= esc($lingkungan->ketua_nama) ?></p>
                        </div>
                        <a href="<?= site_url('admin/wilayah/' . $detail->wilayah->id . '/lingkungan/' . $lingkungan->id) ?>"
                           class="text-xs text-maroon hover:underline">Detail</a>
                    </li>
                <?php endforeach ?>
            </ul>
        <?php endif ?>
    </section>
</div>
<?= $this->endSection() ?>
