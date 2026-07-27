<?php /** @var \App\Entities\Galeri|null $item */ ?>
<?php $isEdit = $item !== null && (int) ($item->id ?? 0) > 0; ?>
<div>
    <h2 class="mb-4 font-display text-xl font-semibold text-maroon"><?= $isEdit ? 'Edit Foto' : 'Tambah Foto' ?></h2>

    <?php if (isset($validation) && $validation->getErrors() !== []): ?>
        <div class="mb-4 rounded border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-800">
            <ul class="list-disc pl-4"><?php foreach ($validation->getErrors() as $error): ?><li><?= esc($error) ?></li><?php endforeach ?></ul>
        </div>
    <?php endif ?>

    <form hx-post="<?= esc($action) ?>" hx-target="#galeri-form-panel" hx-swap="innerHTML" enctype="multipart/form-data" hx-encoding="multipart/form-data" class="space-y-4">
        <?= csrf_field() ?>
        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= esc((string) $item->id) ?>">
            <input type="hidden" name="file_existing" value="<?= esc((string) $item->file_path) ?>">
        <?php endif ?>

        <div>
            <label for="file" class="mb-1 block text-sm font-medium">Foto <?= $isEdit ? '' : '<span class="text-red-600">*</span>' ?></label>
            <?php if ($isEdit && $item->file_path): ?>
                <img src="<?= esc(base_url(ltrim((string) $item->file_path, '/'))) ?>" alt="" class="mb-2 h-32 rounded object-cover">
            <?php endif ?>
            <input type="file" name="file" id="file" accept="image/jpeg,image/png,image/webp" <?= $isEdit ? '' : 'required' ?> class="w-full text-sm">
        </div>

        <div>
            <label for="caption" class="mb-1 block text-sm font-medium">Caption</label>
            <textarea name="caption" id="caption" rows="2" class="w-full rounded border border-stone-300 px-3 py-2 text-sm"><?= esc(old('caption', (string) ($item->caption ?? ''))) ?></textarea>
        </div>

        <?php if (! $isEdit): ?>
            <div>
                <label for="urutan" class="mb-1 block text-sm font-medium">Urutan</label>
                <input type="number" name="urutan" id="urutan" min="0" value="<?= esc(old('urutan', '0')) ?>" class="w-full rounded border border-stone-300 px-3 py-2 text-sm">
            </div>
        <?php endif ?>

        <div class="flex gap-2 pt-2">
            <button type="submit" class="rounded bg-maroon px-4 py-2 text-sm font-medium text-ivory hover:bg-maroon/90">Simpan</button>
            <button type="button" class="rounded border border-stone-300 px-4 py-2 text-sm hover:bg-stone-50"
                    onclick="document.getElementById('galeri-form-panel').innerHTML = '<p class=\'text-sm text-stone-500\'>Form dibatalkan.</p>'">Batal</button>
        </div>
    </form>
</div>
