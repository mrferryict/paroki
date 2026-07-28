<?php /** @var \App\Entities\Dokumen|null $item */ ?>
<?php $isEdit = $item !== null && (int) ($item->id ?? 0) > 0; ?>
<div>
    <h2 class="mb-4 font-display text-xl font-semibold text-maroon"><?= $isEdit ? 'Edit Unduhan' : 'Tambah Unduhan' ?></h2>

    <?php if (isset($validation) && $validation->getErrors() !== []): ?>
        <div class="mb-4 rounded border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-800">
            <ul class="list-disc pl-4"><?php foreach ($validation->getErrors() as $error): ?><li><?= esc($error) ?></li><?php endforeach ?></ul>
        </div>
    <?php endif ?>

    <form hx-post="<?= esc($action) ?>" hx-target="#dokumen-form-panel" hx-swap="innerHTML" enctype="multipart/form-data" hx-encoding="multipart/form-data" class="space-y-4">
        <?= csrf_field() ?>
        <?php if ($isEdit): ?><input type="hidden" name="id" value="<?= esc((string) $item->id) ?>"><?php endif ?>

        <div>
            <label for="nama" class="mb-1 block text-sm font-medium">Nama Unduhan <span class="text-red-600">*</span></label>
            <input type="text" name="nama" id="nama" required value="<?= esc(old('nama', (string) ($item->nama ?? ''))) ?>"
                   class="w-full rounded border border-stone-300 px-3 py-2 text-sm">
        </div>

        <div>
            <label for="kategori" class="mb-1 block text-sm font-medium">Kategori <span class="text-red-600">*</span></label>
            <?php $kategoriValue = old('kategori', (string) ($item->kategori ?? 'formulir')); ?>
            <select name="kategori" id="kategori" required class="w-full rounded border border-stone-300 px-3 py-2 text-sm">
                <?php foreach ($kategoriOptions as $value => $label): ?>
                    <option value="<?= esc($value) ?>" <?= $kategoriValue === $value ? 'selected' : '' ?>><?= esc($label) ?></option>
                <?php endforeach ?>
            </select>
        </div>

        <div>
            <label for="file" class="mb-1 block text-sm font-medium">Berkas <?= $isEdit ? '' : '<span class="text-red-600">*</span>' ?></label>
            <?php if ($isEdit): ?>
                <p class="mb-2 text-xs text-stone-500">Unduh saat ini: <a href="<?= site_url('dokumen/' . $item->id . '/unduh') ?>" class="text-maroon hover:underline" target="_blank" rel="noopener"><?= esc($item->nama) ?></a></p>
            <?php endif ?>
            <input type="file" name="file" id="file" <?= $isEdit ? '' : 'required' ?>
                   accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" class="w-full text-sm">
            <?php if ($isEdit): ?>
                <p class="mt-1 text-xs text-stone-500">Kosongkan jika tidak mengganti berkas.</p>
            <?php endif ?>
        </div>

        <div class="flex gap-2 pt-2">
            <button type="submit" class="rounded bg-maroon px-4 py-2 text-sm font-medium text-ivory hover:bg-maroon/90">Simpan</button>
            <button type="button" class="rounded border border-stone-300 px-4 py-2 text-sm hover:bg-stone-50"
                    onclick="document.getElementById('dokumen-form-panel').innerHTML = '<p class=\'text-sm text-stone-500\'>Form dibatalkan.</p>'">Batal</button>
        </div>
    </form>
</div>
