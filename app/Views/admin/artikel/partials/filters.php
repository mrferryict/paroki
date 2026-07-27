<?php /** @var \App\DTOs\Shared\ContentListFilterDto $filter */ ?>
<?php /** @var string|null $activeKategori */ ?>
<?php
$listUrl = $activeKategori
    ? site_url('admin/artikel/kategori/' . $activeKategori)
    : site_url('admin/artikel');
?>
<form id="artikel-filters" class="rounded-lg border border-gold/20 bg-white p-4 shadow-sm"
      hx-get="<?= esc($listUrl) ?>" hx-target="#artikel-list" hx-swap="outerHTML">
    <div class="grid gap-3 sm:grid-cols-2">
        <?php if ($activeKategori === null): ?>
            <div>
                <label for="filter_kategori" class="mb-1 block text-xs font-semibold uppercase text-stone-500">Kategori</label>
                <select name="kategori" id="filter_kategori" class="w-full rounded border border-stone-300 px-3 py-2 text-sm">
                    <option value="">Semua kategori</option>
                    <?php foreach ($kategoriOptions as $value => $label): ?>
                        <option value="<?= esc($value) ?>" <?= ($filter->kategori ?? '') === $value ? 'selected' : '' ?>><?= esc($label) ?></option>
                    <?php endforeach ?>
                </select>
            </div>
        <?php endif ?>
        <div>
            <label for="filter_status" class="mb-1 block text-xs font-semibold uppercase text-stone-500">Status</label>
            <select name="status" id="filter_status" class="w-full rounded border border-stone-300 px-3 py-2 text-sm">
                <option value="">Semua status</option>
                <?php foreach ($statusOptions as $value => $label): ?>
                    <option value="<?= esc($value) ?>" <?= ($filter->status ?? '') === $value ? 'selected' : '' ?>><?= esc($label) ?></option>
                <?php endforeach ?>
            </select>
        </div>
    </div>
</form>
<script>
    document.getElementById('artikel-filters')?.querySelectorAll('select').forEach(el => {
        el.addEventListener('change', () => htmx.trigger('#artikel-filters', 'submit'));
    });
</script>
