<?php /** @var \App\Entities\ArtikelKategoriRecord|null $item */ ?>
<?php $isEdit = $item !== null && (int) ($item->id ?? 0) > 0; ?>
<div>
    <h2 class="mb-4 font-display text-xl font-semibold text-maroon"><?= $isEdit ? 'Edit Kategori' : 'Tambah Kategori' ?></h2>

    <?php if ($isEdit): ?>
        <p class="mb-4 text-xs text-stone-500">Slug: <code class="rounded bg-stone-100 px-1"><?= esc((string) $item->slug) ?></code></p>
    <?php endif ?>

    <?php if (isset($validation) && $validation->getErrors() !== []): ?>
        <div class="mb-4 rounded border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-800">
            <ul class="list-disc pl-4"><?php foreach ($validation->getErrors() as $error): ?><li><?= esc($error) ?></li><?php endforeach ?></ul>
        </div>
    <?php endif ?>

    <form hx-post="<?= esc($action) ?>" hx-target="#katekese-kategori-form-panel" hx-swap="innerHTML" class="space-y-4">
        <?= csrf_field() ?>
        <?php if ($isEdit): ?><input type="hidden" name="id" value="<?= esc((string) $item->id) ?>"><?php endif ?>

        <div>
            <label for="label" class="mb-1 block text-sm font-medium">Label <span class="text-red-600">*</span></label>
            <input type="text" name="label" id="label" required maxlength="255"
                   value="<?= esc(old('label', (string) ($item->label ?? ''))) ?>"
                   class="w-full rounded border border-stone-300 px-3 py-2 text-sm">
            <?php if (! $isEdit): ?>
                <p class="mt-1 text-xs text-stone-500">Slug akan dibuat otomatis dari label.</p>
            <?php endif ?>
        </div>

        <?php if (! $isEdit): ?>
            <div>
                <label for="urutan" class="mb-1 block text-sm font-medium">Urutan</label>
                <input type="number" name="urutan" id="urutan" min="0" value="<?= esc(old('urutan', '0')) ?>" class="w-full rounded border border-stone-300 px-3 py-2 text-sm">
            </div>
        <?php endif ?>

        <div>
            <label for="is_active" class="mb-1 block text-sm font-medium">Status</label>
            <?php $isActiveDefault = $item !== null ? (bool) $item->is_active : true; ?>
            <?php $isActiveValue = old('is_active') !== null ? old('is_active') : ($isActiveDefault ? '1' : '0'); ?>
            <select name="is_active" id="is_active" class="w-full rounded border border-stone-300 px-3 py-2 text-sm">
                <option value="1" <?= $isActiveValue === '1' ? 'selected' : '' ?>>Aktif</option>
                <option value="0" <?= $isActiveValue === '0' ? 'selected' : '' ?>>Nonaktif</option>
            </select>
        </div>

        <div class="flex gap-2 pt-2">
            <button type="submit" class="rounded bg-maroon px-4 py-2 text-sm font-medium text-ivory hover:bg-maroon/90">Simpan</button>
            <button type="button" class="rounded border border-stone-300 px-4 py-2 text-sm hover:bg-stone-50"
                    onclick="document.getElementById('katekese-kategori-form-panel').innerHTML = '<p class=\'text-sm text-stone-500\'>Form dibatalkan.</p>'">Batal</button>
        </div>
    </form>
</div>
