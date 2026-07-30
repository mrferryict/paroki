<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="mx-auto max-w-2xl space-y-6">
    <div>
        <h1 class="font-display text-3xl font-semibold text-maroon">Pengaturan Situs</h1>
        <p class="mt-1 text-sm text-stone-600">Kelola identitas paroki yang tampil di situs publik. Hanya super admin.</p>
    </div>

    <?php if ($message = session()->getFlashdata('success')): ?>
        <div class="rounded border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800"><?= esc($message) ?></div>
    <?php endif ?>

    <?php if ($message = session()->getFlashdata('error')): ?>
        <div class="rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"><?= esc($message) ?></div>
    <?php endif ?>

    <?php if ($errors = session()->getFlashdata('errors')): ?>
        <div class="rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc pl-4">
                <?php foreach ($errors as $error): ?>
                    <li><?= esc(is_array($error) ? implode(', ', $error) : (string) $error) ?></li>
                <?php endforeach ?>
            </ul>
        </div>
    <?php endif ?>

    <section class="rounded-lg border border-gold/20 bg-white p-6 shadow-sm">
        <h2 class="font-display text-xl font-semibold text-maroon">Informasi Paroki</h2>
        <p class="mt-2 text-sm text-stone-600">Nama paroki dan teks copyright di footer situs publik.</p>

        <form action="<?= site_url('admin/pengaturan/situs') ?>" method="post" class="mt-6 space-y-4">
            <?= csrf_field() ?>
            <div>
                <label for="site_name" class="mb-1 block text-sm font-medium">Nama Paroki</label>
                <input type="text"
                       name="site_name"
                       id="site_name"
                       required
                       maxlength="255"
                       value="<?= esc(old('site_name', $siteName), 'attr') ?>"
                       class="w-full rounded-lg border border-stone-300 px-3 py-2.5 text-sm focus:border-maroon focus:outline-none focus:ring-1 focus:ring-maroon">
            </div>
            <div>
                <label for="copyright_text" class="mb-1 block text-sm font-medium">Teks Copyright (footer)</label>
                <input type="text"
                       name="copyright_text"
                       id="copyright_text"
                       required
                       maxlength="500"
                       value="<?= esc(old('copyright_text', $copyrightText), 'attr') ?>"
                       class="w-full rounded-lg border border-stone-300 px-3 py-2.5 text-sm focus:border-maroon focus:outline-none focus:ring-1 focus:ring-maroon">
                <p class="mt-1 text-xs text-stone-500">Ditampilkan sebagai: © <?= esc(date('Y')) ?> [Nama Paroki]. [Teks ini].</p>
            </div>
            <button type="submit" class="rounded bg-maroon px-4 py-2 text-sm font-medium text-ivory hover:bg-maroon/90">
                Simpan Informasi
            </button>
        </form>
    </section>

    <section class="rounded-lg border border-gold/20 bg-white p-6 shadow-sm">
        <h2 class="font-display text-xl font-semibold text-maroon">Logo Paroki</h2>
        <p class="mt-2 text-sm text-stone-600">Unggah logo dalam format PNG atau JPG. Logo ditampilkan di sisi kiri menubar.</p>

        <div class="mt-6 flex flex-wrap items-center gap-6">
            <div class="flex h-24 w-24 items-center justify-center overflow-hidden rounded-xl border border-gold/20 bg-ivory">
                <?php if ($logoUrl !== null): ?>
                    <img src="<?= esc($logoUrl) ?>" alt="Logo paroki saat ini" class="h-full w-full object-contain p-2">
                <?php else: ?>
                    <span class="px-2 text-center text-xs text-stone-400">Belum ada logo</span>
                <?php endif ?>
            </div>

            <form action="<?= site_url('admin/pengaturan/logo') ?>" method="post" enctype="multipart/form-data" class="min-w-[16rem] flex-1 space-y-3">
                <?= csrf_field() ?>
                <div>
                    <label for="logo" class="mb-1 block text-sm font-medium">Pilih file logo</label>
                    <input type="file" name="logo" id="logo" required accept="image/jpeg,image/png,image/jpg"
                           class="w-full text-sm">
                    <p class="mt-1 text-xs text-stone-500">Maks. 2 MB. Disarankan rasio persegi atau landscape.</p>
                </div>
                <button type="submit" class="rounded bg-maroon px-4 py-2 text-sm font-medium text-ivory hover:bg-maroon/90">
                    Simpan Logo
                </button>
            </form>
        </div>

        <?php if ($logoUrl !== null): ?>
            <form action="<?= site_url('admin/pengaturan/logo/hapus') ?>" method="post" class="mt-6 border-t border-gold/10 pt-6"
                  onsubmit="return confirm('Hapus logo paroki dari menubar?');">
                <?= csrf_field() ?>
                <button type="submit" class="rounded border border-red-200 px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-50">
                    Hapus Logo
                </button>
            </form>
        <?php endif ?>
    </section>
</div>
<?= $this->endSection() ?>
