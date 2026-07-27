<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="font-display text-3xl font-semibold text-maroon">Katekese & Renungan</h1>
            <p class="mt-1 text-sm text-stone-600">Satu modul artikel untuk empat kategori konten iman.</p>
        </div>
        <?php $newUrl = $activeKategori
            ? site_url('admin/artikel/kategori/' . $activeKategori . '/new')
            : site_url('admin/artikel/new'); ?>
        <button type="button" class="rounded bg-maroon px-4 py-2 text-sm font-medium text-ivory hover:bg-maroon/90"
                hx-get="<?= $newUrl ?>" hx-target="#artikel-form-panel" hx-swap="innerHTML">+ Tambah Artikel</button>
    </div>

    <?php if ($message = session()->getFlashdata('success')): ?>
        <div class="rounded border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800"><?= esc($message) ?></div>
    <?php endif ?>

    <nav class="flex flex-wrap gap-2">
        <a href="<?= site_url('admin/artikel') ?>"
           class="rounded px-3 py-1.5 text-sm <?= $activeKategori === null ? 'bg-maroon text-ivory' : 'border border-stone-200 hover:bg-stone-50' ?>">Semua</a>
        <?php foreach ($kategoriOptions as $value => $label): ?>
            <a href="<?= site_url('admin/artikel/kategori/' . $value) ?>"
               class="rounded px-3 py-1.5 text-sm <?= $activeKategori === $value ? 'bg-maroon text-ivory' : 'border border-stone-200 hover:bg-stone-50' ?>"><?= esc($label) ?></a>
        <?php endforeach ?>
    </nav>

    <div class="grid gap-6 lg:grid-cols-5">
        <section class="lg:col-span-3 space-y-4">
            <?= view('admin/artikel/partials/filters', [
                'filter'          => $filter,
                'activeKategori'  => $activeKategori,
                'kategoriOptions' => $kategoriOptions,
                'statusOptions'   => $statusOptions,
            ]) ?>
            <?= view('admin/artikel/partials/list', compact('result', 'filter', 'activeKategori', 'kategoriOptions', 'statusOptions')) ?>
        </section>
        <section class="lg:col-span-2">
            <div id="artikel-form-panel" class="rounded-lg border border-gold/20 bg-white p-4 shadow-sm">
                <p class="text-sm text-stone-500">Pilih artikel untuk diedit, atau klik <strong>Tambah Artikel</strong>.</p>
            </div>
        </section>
    </div>
</div>
<?= $this->endSection() ?>
