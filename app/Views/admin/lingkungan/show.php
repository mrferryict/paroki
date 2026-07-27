<?php /** @var \App\Entities\Wilayah $wilayah */ ?>
<?php /** @var \App\DTOs\Lingkungan\LingkunganDetailDto $detail */ ?>
<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <div class="flex flex-wrap items-center gap-3">
        <a href="<?= site_url('admin/wilayah/' . $wilayah->id . '/lingkungan') ?>" class="text-sm text-maroon hover:underline">&larr; Kembali ke Lingkungan</a>
    </div>

    <div class="rounded-lg border border-gold/20 bg-white p-6 shadow-sm">
        <p class="text-xs uppercase tracking-wide text-stone-500">Wilayah <?= esc($wilayah->nama) ?></p>
        <h1 class="mt-1 font-display text-3xl font-semibold text-maroon"><?= esc($detail->lingkungan->nama) ?></h1>

        <dl class="mt-6 grid gap-4 sm:grid-cols-2">
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-stone-500">Ketua Lingkungan</dt>
                <dd class="mt-1 text-base text-stone-800"><?= esc($detail->lingkungan->ketua_nama) ?></dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-stone-500">Nomor Kontak</dt>
                <dd class="mt-1 font-mono text-base text-stone-800">
                    <?= $detail->ketuaKontak !== null && $detail->ketuaKontak !== '' ? esc($detail->ketuaKontak) : '—' ?>
                </dd>
            </div>
        </dl>
    </div>
</div>
<?= $this->endSection() ?>
