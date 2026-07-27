<?php /** @var \App\Entities\JadwalMisa|null $item */ ?>
<?php $isEdit = $item !== null && (int) ($item->id ?? 0) > 0; ?>
<?php
$jamValue = old('jam');
if ($jamValue === null && $item !== null && $item->jam) {
    $jamValue = substr((string) $item->jam, 0, 5);
}
?>
<div>
    <h2 class="mb-4 font-display text-xl font-semibold text-maroon"><?= $isEdit ? 'Edit Jadwal' : 'Tambah Jadwal' ?></h2>

    <?php if (isset($validation) && $validation->getErrors() !== []): ?>
        <div class="mb-4 rounded border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-800">
            <ul class="list-disc pl-4"><?php foreach ($validation->getErrors() as $error): ?><li><?= esc($error) ?></li><?php endforeach ?></ul>
        </div>
    <?php endif ?>

    <form hx-post="<?= esc($action) ?>" hx-target="#jadwal-misa-form-panel" hx-swap="innerHTML" class="space-y-4">
        <?= csrf_field() ?>
        <?php if ($isEdit): ?><input type="hidden" name="id" value="<?= esc((string) $item->id) ?>"><?php endif ?>

        <div>
            <label for="jenis" class="mb-1 block text-sm font-medium">Jenis <span class="text-red-600">*</span></label>
            <select name="jenis" id="jenis" required class="w-full rounded border border-stone-300 px-3 py-2 text-sm">
                <?php foreach ($jenisOptions as $value => $label): ?>
                    <option value="<?= esc($value) ?>" <?= old('jenis', (string) ($item->jenis ?? '')) === $value ? 'selected' : '' ?>><?= esc($label) ?></option>
                <?php endforeach ?>
            </select>
        </div>

        <div>
            <label for="hari_label" class="mb-1 block text-sm font-medium">Hari / Label <span class="text-red-600">*</span></label>
            <input type="text" name="hari_label" id="hari_label" required placeholder="Mis. Minggu, Senin–Jumat"
                   value="<?= esc(old('hari_label', (string) ($item->hari_label ?? ''))) ?>"
                   class="w-full rounded border border-stone-300 px-3 py-2 text-sm">
        </div>

        <div>
            <label for="jam" class="mb-1 block text-sm font-medium">Jam <span class="text-red-600">*</span></label>
            <input type="time" name="jam" id="jam" required value="<?= esc((string) ($jamValue ?? '')) ?>"
                   class="w-full rounded border border-stone-300 px-3 py-2 text-sm">
        </div>

        <div>
            <label for="catatan" class="mb-1 block text-sm font-medium">Catatan</label>
            <textarea name="catatan" id="catatan" rows="2" class="w-full rounded border border-stone-300 px-3 py-2 text-sm"><?= esc(old('catatan', (string) ($item->catatan ?? ''))) ?></textarea>
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
                    onclick="document.getElementById('jadwal-misa-form-panel').innerHTML = '<p class=\'text-sm text-stone-500\'>Form dibatalkan.</p>'">Batal</button>
        </div>
    </form>
</div>
