<?php /** @var \App\Entities\SakramenJenis|null $item */ ?>
<?php $isEdit = $item !== null && (int) ($item->id ?? 0) > 0; ?>
<div>
    <h2 class="mb-4 font-display text-xl font-semibold text-maroon"><?= $isEdit ? 'Edit Jenis' : 'Tambah Jenis' ?></h2>

    <?php if (isset($validation) && $validation->getErrors() !== []): ?>
        <div class="mb-4 rounded border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-800">
            <ul class="list-disc pl-4"><?php foreach ($validation->getErrors() as $error): ?><li><?= esc($error) ?></li><?php endforeach ?></ul>
        </div>
    <?php endif ?>

    <form hx-post="<?= esc($action) ?>" hx-target="#sakramen-jenis-form-panel" hx-swap="innerHTML" class="space-y-4">
        <?= csrf_field() ?>
        <?php if ($isEdit): ?><input type="hidden" name="id" value="<?= esc((string) $item->id) ?>"><?php endif ?>

        <div>
            <label for="kode" class="mb-1 block text-sm font-medium">Kode <?= $isEdit ? '' : '<span class="text-red-600">*</span>' ?></label>
            <?php if ($isEdit): ?>
                <input type="text" readonly value="<?= esc($kodeOptions[$item->kode] ?? $item->kode) ?>" class="w-full rounded border border-stone-200 bg-stone-50 px-3 py-2 text-sm">
                <input type="hidden" name="kode" value="<?= esc((string) $item->kode) ?>">
            <?php else: ?>
                <select name="kode" id="kode" required class="w-full rounded border border-stone-300 px-3 py-2 text-sm">
                    <option value="">— Pilih kode —</option>
                    <?php foreach ($kodeOptions as $value => $label): ?>
                        <option value="<?= esc($value) ?>" <?= old('kode', (string) ($item->kode ?? '')) === $value ? 'selected' : '' ?>><?= esc($label) ?></option>
                    <?php endforeach ?>
                </select>
            <?php endif ?>
        </div>

        <div>
            <label for="nama" class="mb-1 block text-sm font-medium">Nama <span class="text-red-600">*</span></label>
            <input type="text" name="nama" id="nama" required value="<?= esc(old('nama', (string) ($item->nama ?? ''))) ?>" class="w-full rounded border border-stone-300 px-3 py-2 text-sm">
        </div>

        <div>
            <label for="deskripsi" class="mb-1 block text-sm font-medium">Deskripsi</label>
            <textarea name="deskripsi" id="deskripsi" rows="3" class="w-full rounded border border-stone-300 px-3 py-2 text-sm"><?= esc(old('deskripsi', (string) ($item->deskripsi ?? ''))) ?></textarea>
        </div>

        <div>
            <label for="icon" class="mb-1 block text-sm font-medium">Icon (slug SVG) <span class="text-red-600">*</span></label>
            <input type="text" name="icon" id="icon" required value="<?= esc(old('icon', (string) ($item->icon ?? ''))) ?>" class="w-full rounded border border-stone-300 px-3 py-2 text-sm">
        </div>

        <?php if (! $isEdit): ?>
            <div>
                <label for="urutan" class="mb-1 block text-sm font-medium">Urutan</label>
                <input type="number" name="urutan" id="urutan" min="0" value="<?= esc(old('urutan', '0')) ?>" class="w-full rounded border border-stone-300 px-3 py-2 text-sm">
            </div>
        <?php endif ?>

        <div class="flex items-center gap-2">
            <input type="hidden" name="is_active" value="0">
            <?php $isActiveDefault = $item !== null ? (bool) $item->is_active : true; ?>
            <?php $isActiveChecked = old('is_active') !== null ? old('is_active') === '1' : $isActiveDefault; ?>
            <input type="checkbox" name="is_active" id="is_active" value="1" <?= $isActiveChecked ? 'checked' : '' ?> class="rounded border-stone-300 text-maroon">
            <label for="is_active" class="text-sm">Aktif</label>
        </div>

        <div class="flex gap-2 pt-2">
            <button type="submit" class="rounded bg-maroon px-4 py-2 text-sm font-medium text-ivory hover:bg-maroon/90">Simpan</button>
            <button type="button" class="rounded border border-stone-300 px-4 py-2 text-sm hover:bg-stone-50"
                    onclick="document.getElementById('sakramen-jenis-form-panel').innerHTML = '<p class=\'text-sm text-stone-500\'>Form dibatalkan.</p>'">Batal</button>
        </div>
    </form>
</div>
