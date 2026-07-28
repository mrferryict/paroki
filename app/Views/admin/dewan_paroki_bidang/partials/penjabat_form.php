<?php /** @var \App\Entities\DewanParokiBidang $bidang */ ?>
<?php /** @var \App\Entities\DewanParokiPenjabat|null $item */ ?>
<?php $isEdit = $item !== null && (int) ($item->id ?? 0) > 0; ?>
<div>
    <h2 class="mb-4 font-display text-xl font-semibold text-maroon"><?= $isEdit ? 'Edit Penjabat' : 'Tambah Penjabat' ?></h2>
    <p class="mb-4 text-xs text-stone-500">Bidang: <?= esc($bidang->nama_tampilan) ?></p>

    <?php if (isset($validation) && $validation->getErrors() !== []): ?>
        <div class="mb-4 rounded border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-800">
            <ul class="list-disc pl-4"><?php foreach ($validation->getErrors() as $error): ?><li><?= esc($error) ?></li><?php endforeach ?></ul>
        </div>
    <?php endif ?>

    <form hx-post="<?= esc($action) ?>" hx-target="#dewan-paroki-form-panel" hx-swap="innerHTML" class="space-y-4">
        <?= csrf_field() ?>
        <?php if ($isEdit): ?><input type="hidden" name="id" value="<?= esc((string) $item->id) ?>"><?php endif ?>

        <div>
            <label for="nama" class="mb-1 block text-sm font-medium">Nama <span class="text-red-600">*</span></label>
            <input type="text" name="nama" id="nama" required
                   value="<?= esc(old('nama', (string) ($item->nama ?? ''))) ?>"
                   class="w-full rounded border border-stone-300 px-3 py-2 text-sm">
        </div>

        <div>
            <label for="whatsapp" class="mb-1 block text-sm font-medium">
                No. WhatsApp <?= $isEdit ? '' : '<span class="text-red-600">*</span>' ?>
            </label>
            <input type="tel" name="whatsapp" id="whatsapp" <?= $isEdit ? '' : 'required' ?>
                   placeholder="<?= $isEdit ? 'Kosongkan jika tidak diubah' : '081234567890' ?>"
                   value="<?= esc(old('whatsapp', '')) ?>"
                   class="w-full rounded border border-stone-300 px-3 py-2 text-sm">
        </div>

        <div class="flex gap-2 pt-2">
            <button type="submit" class="rounded bg-maroon px-4 py-2 text-sm font-medium text-ivory hover:bg-maroon/90">Simpan</button>
            <button type="button" class="rounded border border-stone-300 px-4 py-2 text-sm hover:bg-stone-50"
                    onclick="document.getElementById('dewan-paroki-form-panel').innerHTML = '<p class=\'text-sm text-stone-500\'>Form dibatalkan.</p>'">Batal</button>
        </div>
    </form>
</div>
