<?php /** @var \App\Entities\Berita|null $item */ ?>
<?php $isEdit = $item !== null && (int) ($item->id ?? 0) > 0; ?>
<?php
$kategoriValue = old('kategori') ?? ($item !== null
    ? ($item->kategori instanceof \App\Enums\BeritaKategori ? $item->kategori->value : (string) $item->kategori)
    : '');
$statusValue = old('status') ?? ($item !== null
    ? ($item->status instanceof \App\Enums\PublishStatus ? $item->status->value : (string) $item->status)
    : 'draft');
$tanggalValue = old('tanggal_terbit');
if ($tanggalValue === null && $item !== null && $item->tanggal_terbit) {
    $tanggalValue = $item->tanggal_terbit->format('Y-m-d\TH:i');
}
?>
<div>
    <h2 class="mb-4 font-display text-xl font-semibold text-maroon"><?= $isEdit ? 'Edit Berita' : 'Tambah Berita' ?></h2>

    <?php if (isset($errorMessage) && $errorMessage !== ''): ?>
        <div class="mb-4 rounded border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-800"><?= esc($errorMessage) ?></div>
    <?php endif ?>

    <?php if (isset($validation) && $validation->getErrors() !== []): ?>
        <div class="mb-4 rounded border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-800">
            <ul class="list-disc pl-4"><?php foreach ($validation->getErrors() as $error): ?><li><?= esc($error) ?></li><?php endforeach ?></ul>
        </div>
    <?php endif ?>

    <form hx-post="<?= esc($action) ?>"
          hx-target="#berita-form-panel"
          hx-swap="innerHTML"
          hx-encoding="multipart/form-data"
          class="space-y-4">
        <?= csrf_field() ?>
        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= esc((string) $item->id) ?>">
            <input type="hidden" name="gambar_existing" value="<?= esc((string) ($item->gambar_utama ?? '')) ?>">
        <?php endif ?>

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
            <p class="mt-1 text-xs text-stone-500">Kosongkan untuk memakai waktu saat ini jika status Terbit.</p>
        </div>

        <div>
            <label for="tags" class="mb-1 block text-sm font-medium">Tag</label>
            <input type="text" name="tags" id="tags"
                   placeholder="Mis. natal, kegiatan, paroki"
                   value="<?= esc(old('tags', (string) ($item->tags ?? ''))) ?>"
                   class="w-full rounded border border-stone-300 px-3 py-2 text-sm">
            <p class="mt-1 text-xs text-stone-500">Pisahkan dengan koma. Digunakan untuk pencarian di halaman berita.</p>
        </div>

        <div>
            <label for="ringkasan" class="mb-1 block text-sm font-medium">Ringkasan</label>
            <textarea name="ringkasan" id="ringkasan" rows="2" class="w-full rounded border border-stone-300 px-3 py-2 text-sm"><?= esc(old('ringkasan', (string) ($item->ringkasan ?? ''))) ?></textarea>
        </div>

        <div>
            <label for="konten" class="mb-1 block text-sm font-medium">Konten</label>
            <textarea name="konten" id="konten" rows="6" class="w-full rounded border border-stone-300 px-3 py-2 text-sm font-mono text-sm"><?= esc(old('konten', (string) ($item->konten ?? ''))) ?></textarea>
        </div>

        <div>
            <label for="gambar_utama" class="mb-1 block text-sm font-medium">Gambar Utama <?= $isEdit ? '' : '<span class="text-red-600">*</span>' ?></label>
            <?php if ($isEdit && ! empty($item->gambar_utama)): ?>
                <img src="<?= esc(base_url((string) $item->gambar_utama)) ?>" alt="" class="mb-2 h-24 rounded object-cover">
            <?php endif ?>
            <input type="file" name="gambar_utama" id="gambar_utama" accept="image/jpeg,image/png,image/webp"
                   <?= $isEdit ? '' : 'required' ?> class="w-full text-sm">
            <p class="mt-1 text-xs text-stone-500">JPEG, PNG, atau WebP. Maks. 2 MB.</p>
        </div>

        <div class="flex gap-2 pt-2">
            <button type="submit" class="rounded bg-maroon px-4 py-2 text-sm font-medium text-ivory hover:bg-maroon/90">Simpan</button>
            <button type="button" class="rounded border border-stone-300 px-4 py-2 text-sm hover:bg-stone-50"
                    onclick="document.getElementById('berita-form-panel').innerHTML = '<p class=\'text-sm text-stone-500\'>Form dibatalkan.</p>'">Batal</button>
        </div>
    </form>
</div>
