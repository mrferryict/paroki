<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="font-display text-3xl font-semibold text-maroon">Wilayah & Lingkungan</h1>
            <p class="mt-1 text-sm text-stone-600">Kelola wilayah paroki, koordinator, dan lingkungan beserta ketuanya.</p>
        </div>
        <button type="button" class="rounded bg-maroon px-4 py-2 text-sm font-medium text-ivory hover:bg-maroon/90"
                hx-get="<?= site_url('admin/wilayah/new') ?>" hx-target="#wilayah-form-panel" hx-swap="innerHTML">+ Tambah Wilayah</button>
    </div>

    <?php if ($message = session()->getFlashdata('success')): ?>
        <div class="rounded border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800"><?= esc($message) ?></div>
    <?php endif ?>

    <div class="grid gap-6 xl:grid-cols-5">
        <section class="xl:col-span-3">
            <?= view('admin/wilayah/partials/table', ['rows' => $rows]) ?>
        </section>
        <section class="xl:col-span-2">
            <div id="wilayah-form-panel" class="rounded-lg border border-gold/20 bg-white p-4 shadow-sm">
                <p class="text-sm text-stone-500">Pilih baris untuk diedit, atau klik <strong>Tambah Wilayah</strong> / <strong>+ Lingkungan</strong>.</p>
            </div>
        </section>
    </div>
</div>
<?= $this->endSection() ?>
