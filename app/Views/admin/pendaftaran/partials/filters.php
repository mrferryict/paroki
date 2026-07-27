<?php /** @var \App\DTOs\Pendaftaran\PendaftaranListFilterDto $filter */ ?>
<form id="pendaftaran-filters" class="rounded-lg border border-gold/20 bg-white p-4 shadow-sm"
      hx-get="<?= site_url('admin/pendaftaran') ?>" hx-target="#pendaftaran-list" hx-swap="outerHTML">
    <div>
        <label for="filter_status" class="mb-1 block text-xs font-semibold uppercase text-stone-500">Status</label>
        <select name="status" id="filter_status" class="w-full max-w-xs rounded border border-stone-300 px-3 py-2 text-sm">
            <option value="">Semua status</option>
            <?php foreach ($statusOptions as $value => $label): ?>
                <option value="<?= esc($value) ?>" <?= ($filter->status ?? '') === $value ? 'selected' : '' ?>><?= esc($label) ?></option>
            <?php endforeach ?>
        </select>
    </div>
</form>
<script>
    document.getElementById('pendaftaran-filters')?.querySelector('select')?.addEventListener('change', () => {
        htmx.trigger('#pendaftaran-filters', 'submit');
    });
</script>
