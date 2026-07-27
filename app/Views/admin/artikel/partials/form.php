<?php /** @var \App\Entities\Artikel|null $item */ ?>
<?php /** @var string|null $defaultKategori */ ?>
<?php $isEdit = $item !== null && (int) ($item->id ?? 0) > 0; ?>
<?php
$kategoriValue = old('kategori') ?? ($item !== null
    ? ($item->kategori instanceof \App\Enums\ArtikelKategori ? $item->kategori->value : (string) $item->kategori)
    : ($defaultKategori ?? ''));
$statusValue = old('status') ?? ($item !== null
    ? ($item->status instanceof \App\Enums\PublishStatus ? $item->status->value : (string) $item->status)
    : 'draft');
$tanggalValue = old('tanggal_terbit');
if ($tanggalValue === null && $item !== null && $item->tanggal_terbit) {
    $tanggalValue = $item->tanggal_terbit->format('Y-m-d\TH:i');
}
?>
<div>
    <h2 class="mb-4 font-display text-xl font-semibold text-maroon"><?= $isEdit ? 'Edit Artikel' : 'Tambah Artikel' ?></h2>

    <?php if (isset($validation) && $validation->getErrors() !== []): ?>
        <div class="mb-4 rounded border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-800">
            <ul class="list-disc pl-4"><?php foreach ($validation->getErrors() as $error): ?><li><?= esc($error) ?></li><?php endforeach ?></ul>
        </div>
    <?php endif ?>

    <form hx-post="<?= esc($action) ?>" hx-target="#artikel-form-panel" hx-swap="innerHTML" class="space-y-4">
        <?= csrf_field() ?>
        <?php if ($isEdit): ?><input type="hidden" name="id" value="<?= esc((string) $item->id) ?>"><?php endif ?>

        <div>
            <label for="judul" class="mb-1 block text-sm font-medium">Judul <span class="text-red-600">*</span></label>
            <input type="text" name="judul" id="judul" required value="<?= esc(old('judul', (string) ($item->judul ?? ''))) ?>"
                   class="w-full rounded border border-stone-300 px-3 py-2 text-sm">
        </div>

        <div class="grid gap-3 sm:grid-cols-2">
            <div>
                <label for="kategori" class="mb-1 block text-sm font-medium">Kategori <span class="text-red-600">*</span></label>
                <select name="kategori" id="kategori" required class="w-full rounded border border-stone-300 px-3 py-2 text-sm">
                    <?php foreach ($kategoriOptions as $value => $label): ?>
                        <option value="<?= esc($value) ?>" <?= $kategoriValue === $value ? 'selected' : '' ?>><?= esc($label) ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div>
                <label for="status" class="mb-1 block text-sm font-medium">Status <span class="text-red-600">*</span></label>
                <select name="status" id="status" required class="w-full rounded border border-stone-300 px-3 py-2 text-sm">
                    <?php foreach ($statusOptions as $value => $label): ?>
                        <option value="<?= esc($value) ?>" <?= $statusValue === $value ? 'selected' : '' ?>><?= esc($label) ?></option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>

        <div>
            <label for="tanggal_terbit" class="mb-1 block text-sm font-medium">Tanggal Terbit</label>
            <input type="datetime-local" name="tanggal_terbit" id="tanggal_terbit" value="<?= esc((string) ($tanggalValue ?? '')) ?>"
                   class="w-full rounded border border-stone-300 px-3 py-2 text-sm">
        </div>

        <div>
            <label for="konten" class="mb-1 block text-sm font-medium">Konten</label>
            <textarea name="konten" id="konten" rows="8" class="w-full rounded border border-stone-300 px-3 py-2 text-sm font-mono"><?= esc(old('konten', (string) ($item->konten ?? ''))) ?></textarea>
        </div>

        <div class="flex gap-2 pt-2">
            <button type="submit" class="rounded bg-maroon px-4 py-2 text-sm font-medium text-ivory hover:bg-maroon/90">Simpan</button>
            <button type="button" class="rounded border border-stone-300 px-4 py-2 text-sm hover:bg-stone-50"
                    onclick="document.getElementById('artikel-form-panel').innerHTML = '<p class=\'text-sm text-stone-500\'>Form dibatalkan.</p>'">Batal</button>
        </div>
    </form>
</div>
