<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="font-display text-3xl font-semibold text-maroon">Jadwal Misa</h1>
            <p class="mt-1 text-sm text-stone-600">Kelola jadwal misa harian, mingguan, Jumat pertama, dan khusus.</p>
        </div>
        <button type="button" class="rounded bg-maroon px-4 py-2 text-sm font-medium text-ivory hover:bg-maroon/90"
                hx-get="<?= site_url('admin/jadwal-misa/new') ?>" hx-target="#jadwal-misa-form-panel" hx-swap="innerHTML">+ Tambah Jadwal</button>
    </div>

    <?php if ($message = session()->getFlashdata('success')): ?>
        <div class="rounded border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800"><?= esc($message) ?></div>
    <?php endif ?>

    <div class="grid gap-6 lg:grid-cols-5">
        <section class="lg:col-span-3">
            <?= view('admin/jadwal_misa/partials/list', ['items' => $items, 'jenisOptions' => $jenisOptions]) ?>
        </section>
        <section class="lg:col-span-2">
            <div id="jadwal-misa-form-panel" class="rounded-lg border border-gold/20 bg-white p-4 shadow-sm">
                <p class="text-sm text-stone-500">Pilih jadwal untuk diedit, atau klik <strong>Tambah Jadwal</strong>.</p>
            </div>
        </section>
    </div>
</div>
<?= $this->endSection() ?>
