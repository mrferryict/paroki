<?php
/** @var \App\Entities\Galeri|null $item */
/** @var \App\Entities\GaleriEvent $event */
/** @var array<string, string> $jenisOptions */
?>
<?php $isEdit = $item !== null && (int) ($item->id ?? 0) > 0; ?>
<?php $selectedJenis = old('jenis', (string) ($item->jenis ?? 'foto')); ?>
<div x-data="{ jenis: '<?= esc($selectedJenis, 'attr') ?>' }">
    <h2 class="mb-1 font-display text-xl font-semibold text-maroon"><?= $isEdit ? 'Edit Item' : 'Tambah Item' ?></h2>
    <p class="mb-4 text-sm text-stone-500">Event: <strong><?= esc((string) $event->judul) ?></strong></p>

    <?php if (isset($validation) && $validation->getErrors() !== []): ?>
        <div class="mb-4 rounded border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-800">
            <ul class="list-disc pl-4"><?php foreach ($validation->getErrors() as $error): ?><li><?= esc($error) ?></li><?php endforeach ?></ul>
        </div>
    <?php endif ?>

    <form hx-post="<?= esc($action) ?>" hx-target="#galeri-form-panel" hx-swap="innerHTML"
          enctype="multipart/form-data" hx-encoding="multipart/form-data" class="space-y-4">
        <?= csrf_field() ?>
        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= esc((string) $item->id) ?>">
            <input type="hidden" name="file_existing" value="<?= esc((string) ($item->file_path ?? '')) ?>">
        <?php endif ?>

        <div>
            <label for="jenis" class="mb-1 block text-sm font-medium">Jenis <span class="text-red-600">*</span></label>
            <select name="jenis" id="jenis" x-model="jenis" class="w-full rounded border border-stone-300 px-3 py-2 text-sm">
                <?php foreach ($jenisOptions as $value => $label): ?>
                    <option value="<?= esc($value) ?>" <?= $selectedJenis === $value ? 'selected' : '' ?>><?= esc($label) ?></option>
                <?php endforeach ?>
            </select>
        </div>

        <div x-show="jenis === 'foto'" x-cloak>
            <label for="file" class="mb-1 block text-sm font-medium">Foto <?= $isEdit ? '' : '<span class="text-red-600">*</span>' ?></label>
            <?php if ($isEdit && ! empty($item->file_path)): ?>
                <img src="<?= esc(base_url(ltrim((string) $item->file_path, '/'))) ?>" alt="" class="mb-2 h-32 rounded object-cover">
            <?php endif ?>
            <input type="file" name="file" id="file" accept="image/jpeg,image/png,image/webp" class="w-full text-sm">
            <p class="mt-1 text-xs text-stone-500">JPEG, PNG, atau WebP — maks. 5 MB. Di-resize otomatis ke 1200×900 px.</p>
        </div>

        <div x-show="jenis === 'video'" x-cloak>
            <label for="youtube_url" class="mb-1 block text-sm font-medium">URL YouTube <span class="text-red-600">*</span></label>
            <input type="url" name="youtube_url" id="youtube_url"
                   value="<?= esc(old('youtube_url', (string) ($item->youtube_url ?? ''))) ?>"
                   placeholder="https://www.youtube.com/watch?v=..."
                   class="w-full rounded border border-stone-300 px-3 py-2 text-sm">
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
