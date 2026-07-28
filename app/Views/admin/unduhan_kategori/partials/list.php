<div id="unduhan-kategori-list" class="overflow-hidden rounded-lg border border-gold/20 bg-white shadow-sm">
    <div class="border-b border-gold/10 px-4 py-3">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-stone-600">Daftar Kategori</h2>
    </div>

    <?php if ($items === []): ?>
        <p class="px-4 py-8 text-center text-sm text-stone-500">Belum ada kategori unduhan.</p>
    <?php else: ?>
        <ul class="divide-y divide-stone-100">
            <?php foreach ($items as $item): ?>
                <li class="flex items-start gap-3 px-4 py-3">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="font-medium text-maroon"><?= esc((string) $item->label) ?></p>
                            <?php if ($item->is_active): ?>
                                <span class="rounded bg-green-100 px-2 py-0.5 text-xs text-green-800">Aktif</span>
                            <?php else: ?>
                                <span class="rounded bg-stone-100 px-2 py-0.5 text-xs text-stone-600">Nonaktif</span>
                            <?php endif ?>
                            <span class="font-mono text-xs text-stone-400"><?= esc((string) $item->slug) ?> · #<?= esc((string) $item->urutan) ?></span>
                        </div>
                    </div>
                    <div class="flex shrink-0 gap-1">
                        <button type="button" class="rounded border border-maroon/20 px-2 py-1 text-xs text-maroon hover:bg-maroon/5"
                                hx-get="<?= site_url('admin/unduhan-kategori/' . $item->id . '/edit') ?>"
                                hx-target="#unduhan-kategori-form-panel" hx-swap="innerHTML">Edit</button>
                        <button type="button" class="rounded border border-red-200 px-2 py-1 text-xs text-red-700 hover:bg-red-50"
                                hx-post="<?= site_url('admin/unduhan-kategori/' . $item->id . '/delete') ?>"
                                hx-target="#unduhan-kategori-list" hx-swap="outerHTML"
                                hx-confirm="Hapus kategori ini?">Hapus</button>
                    </div>
                </li>
            <?php endforeach ?>
        </ul>
    <?php endif ?>
</div>
