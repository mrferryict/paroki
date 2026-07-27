<?php
/** @var \App\Entities\HeroSlide|null $slide */
/** @var string $action */
/** @var string $method */
/** @var \CodeIgniter\Validation\ValidationInterface|null $validation */
$isEdit = $slide !== null && (int) ($slide->id ?? 0) > 0;
?>
<div>
    <h2 class="mb-4 font-display text-xl font-semibold text-maroon">
        <?= $isEdit ? 'Edit Slide' : 'Tambah Slide' ?>
    </h2>

    <?php if (isset($validation) && $validation->getErrors() !== []): ?>
        <div class="mb-4 rounded border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-800">
            <ul class="list-disc pl-4">
                <?php foreach ($validation->getErrors() as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach ?>
            </ul>
        </div>
    <?php endif ?>

    <form hx-post="<?= esc($action) ?>"
          hx-encoding="multipart/form-data"
          hx-target="#hero-slide-form-panel"
          hx-swap="innerHTML"
          class="space-y-4">
        <?= csrf_field() ?>

        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= esc((string) $slide->id) ?>">
            <input type="hidden" name="gambar_existing" value="<?= esc((string) $slide->gambar) ?>">
        <?php endif ?>

        <div>
            <label for="eyebrow" class="mb-1 block text-sm font-medium">Eyebrow</label>
            <input type="text" name="eyebrow" id="eyebrow"
                   value="<?= esc(old('eyebrow', (string) ($slide->eyebrow ?? ''))) ?>"
                   class="w-full rounded border border-stone-300 px-3 py-2 text-sm">
        </div>

        <div>
            <label for="judul" class="mb-1 block text-sm font-medium">Judul <span class="text-red-600">*</span></label>
            <textarea name="judul" id="judul" rows="3" required
                      class="w-full rounded border border-stone-300 px-3 py-2 text-sm"><?= esc(old('judul', (string) ($slide->judul ?? ''))) ?></textarea>
            <p class="mt-1 text-xs text-stone-500">Gunakan baris baru untuk judul 2 baris.</p>
        </div>

        <div>
            <label for="subjudul" class="mb-1 block text-sm font-medium">Subjudul</label>
            <textarea name="subjudul" id="subjudul" rows="2"
                      class="w-full rounded border border-stone-300 px-3 py-2 text-sm"><?= esc(old('subjudul', (string) ($slide->subjudul ?? ''))) ?></textarea>
        </div>

        <div class="grid gap-3 sm:grid-cols-2">
            <div>
                <label for="cta1_label" class="mb-1 block text-sm font-medium">CTA 1 Label</label>
                <input type="text" name="cta1_label" id="cta1_label"
                       value="<?= esc(old('cta1_label', (string) ($slide->cta1_label ?? ''))) ?>"
                       class="w-full rounded border border-stone-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label for="cta1_href" class="mb-1 block text-sm font-medium">CTA 1 URL</label>
                <input type="url" name="cta1_href" id="cta1_href"
                       value="<?= esc(old('cta1_href', (string) ($slide->cta1_href ?? ''))) ?>"
                       class="w-full rounded border border-stone-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label for="cta2_label" class="mb-1 block text-sm font-medium">CTA 2 Label</label>
                <input type="text" name="cta2_label" id="cta2_label"
                       value="<?= esc(old('cta2_label', (string) ($slide->cta2_label ?? ''))) ?>"
                       class="w-full rounded border border-stone-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label for="cta2_href" class="mb-1 block text-sm font-medium">CTA 2 URL</label>
                <input type="url" name="cta2_href" id="cta2_href"
                       value="<?= esc(old('cta2_href', (string) ($slide->cta2_href ?? ''))) ?>"
                       class="w-full rounded border border-stone-300 px-3 py-2 text-sm">
            </div>
        </div>

        <div>
            <label for="gambar" class="mb-1 block text-sm font-medium">
                Gambar <?= $isEdit ? '' : '<span class="text-red-600">*</span>' ?>
            </label>
            <?php if ($isEdit && $slide->gambar): ?>
                <img src="<?= esc(base_url((string) $slide->gambar)) ?>" alt="" class="mb-2 h-24 rounded object-cover">
            <?php endif ?>
            <input type="file" name="gambar" id="gambar" accept="image/jpeg,image/png,image/webp"
                   <?= $isEdit ? '' : 'required' ?>
                   class="w-full text-sm">
        </div>

        <?php if (! $isEdit): ?>
            <div>
                <label for="urutan" class="mb-1 block text-sm font-medium">Urutan</label>
                <input type="number" name="urutan" id="urutan" min="0"
                       value="<?= esc(old('urutan', '0')) ?>"
                       class="w-full rounded border border-stone-300 px-3 py-2 text-sm">
                <p class="mt-1 text-xs text-stone-500">Kosongkan/0 untuk menempatkan di akhir.</p>
            </div>
        <?php endif ?>

        <div class="flex items-center gap-2">
            <input type="hidden" name="is_active" value="0">
            <?php
            $isActiveDefault = $slide !== null ? (bool) $slide->is_active : true;
            $isActiveChecked = old('is_active') !== null ? old('is_active') === '1' : $isActiveDefault;
            ?>
            <input type="checkbox" name="is_active" id="is_active" value="1"
                   <?= $isActiveChecked ? 'checked' : '' ?>
                   class="rounded border-stone-300 text-maroon focus:ring-maroon/30">
            <label for="is_active" class="text-sm">Aktif</label>
        </div>

        <div class="flex gap-2 pt-2">
            <button type="submit"
                    class="rounded bg-maroon px-4 py-2 text-sm font-medium text-ivory hover:bg-maroon/90">
                Simpan
            </button>
            <button type="button"
                    class="rounded border border-stone-300 px-4 py-2 text-sm hover:bg-stone-50"
                    onclick="document.getElementById('hero-slide-form-panel').innerHTML = '<p class=\'text-sm text-stone-500\'>Form dibatalkan.</p>'">
                Batal
            </button>
        </div>
    </form>
</div>
