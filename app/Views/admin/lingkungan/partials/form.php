<?php /** @var \App\Entities\Wilayah $wilayah */ ?>
<?php /** @var \App\Entities\Lingkungan|null $item */ ?>
<?php $isEdit = $item !== null && (int) ($item->id ?? 0) > 0; ?>
<div>
    <h2 class="mb-4 font-display text-xl font-semibold text-maroon"><?= $isEdit ? 'Edit Lingkungan' : 'Tambah Lingkungan' ?></h2>
    <p class="mb-4 text-xs text-stone-500">Wilayah: <?= esc($wilayah->nama) ?></p>

    <?php if (isset($validation) && $validation->getErrors() !== []): ?>
        <div class="mb-4 rounded border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-800">
            <ul class="list-disc pl-4"><?php foreach ($validation->getErrors() as $error): ?><li><?= esc($error) ?></li><?php endforeach ?></ul>
        </div>
    <?php endif ?>

    <form hx-post="<?= esc($action) ?>" hx-target="#lingkungan-form-panel" hx-swap="innerHTML" class="space-y-4">
        <?= csrf_field() ?>
        <?php if ($isEdit): ?><input type="hidden" name="id" value="<?= esc((string) $item->id) ?>"><?php endif ?>

        <div>
            <label for="nama" class="mb-1 block text-sm font-medium">Nama Lingkungan <span class="text-red-600">*</span></label>
            <input type="text" name="nama" id="nama" required
                   value="<?= esc(old('nama', (string) ($item->nama ?? ''))) ?>"
                   class="w-full rounded border border-stone-300 px-3 py-2 text-sm">
        </div>

        <div>
            <label for="ketua_nama" class="mb-1 block text-sm font-medium">Nama Ketua <span class="text-red-600">*</span></label>
            <input type="text" name="ketua_nama" id="ketua_nama" required
                   value="<?= esc(old('ketua_nama', (string) ($item->ketua_nama ?? ''))) ?>"
                   class="w-full rounded border border-stone-300 px-3 py-2 text-sm">
        </div>

        <div>
            <label for="ketua_kontak" class="mb-1 block text-sm font-medium">Nomor Kontak Ketua</label>
            <input type="tel" name="ketua_kontak" id="ketua_kontak"
                   placeholder="<?= $isEdit ? 'Kosongkan jika tidak diubah' : 'Opsional' ?>"
                   value="<?= esc(old('ketua_kontak', '')) ?>"
                   class="w-full rounded border border-stone-300 px-3 py-2 text-sm">
            <?php if ($isEdit): ?>
                <p class="mt-1 text-xs text-stone-500">Nomor saat ini hanya tampil di halaman detail.</p>
            <?php endif ?>
        </div>

        <div class="flex gap-2 pt-2">
            <button type="submit" class="rounded bg-maroon px-4 py-2 text-sm font-medium text-ivory hover:bg-maroon/90">Simpan</button>
            <button type="button" class="rounded border border-stone-300 px-4 py-2 text-sm hover:bg-stone-50"
                    onclick="document.getElementById('lingkungan-form-panel').innerHTML = '<p class=\'text-sm text-stone-500\'>Form dibatalkan.</p>'">Batal</button>
        </div>
    </form>
</div>
