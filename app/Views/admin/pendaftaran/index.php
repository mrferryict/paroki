<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <div>
        <h1 class="font-display text-3xl font-semibold text-maroon">Pendaftaran</h1>
        <p class="mt-1 text-sm text-stone-600">Kelola permohonan dari formulir publik. Nomor WhatsApp hanya tampil di halaman detail.</p>
    </div>

    <?php if ($message = session()->getFlashdata('success')): ?>
        <div class="rounded border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800"><?= esc($message) ?></div>
    <?php endif ?>

    <section class="space-y-4">
        <?= view('admin/pendaftaran/partials/filters', [
            'filter'        => $filter,
            'statusOptions' => $statusOptions,
        ]) ?>
        <?= view('admin/pendaftaran/partials/list', compact('result', 'filter', 'statusOptions')) ?>
    </section>
</div>
<?= $this->endSection() ?>
