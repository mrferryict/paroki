<div id="dokumen-list" class="overflow-hidden rounded-lg border border-gold/20 bg-white shadow-sm">
    <div class="border-b border-gold/10 px-4 py-3">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-stone-600">Daftar Dokumen</h2>
    </div>

    <?php if ($items === []): ?>
        <p class="px-4 py-8 text-center text-sm text-stone-500">Belum ada dokumen.</p>
    <?php else: ?>
        <ul class="divide-y divide-stone-100">
            <?php foreach ($items as $item): ?>
                <li class="flex items-start gap-3 px-4 py-3">
                    <div class="min-w-0 flex-1">
                        <p class="font-medium text-maroon"><?= esc($item->nama) ?></p>
                        <p class="mt-1 text-xs text-stone-500"><?= esc($item->kategori) ?></p>
                    </div>
                    <div class="flex shrink-0 flex-col gap-1">
                        <a href="<?= site_url('dokumen/' . $item->id . '/unduh') ?>" target="_blank" rel="noopener"
                           class="rounded border border-stone-200 px-2 py-1 text-center text-xs hover:bg-stone-50">Unduh</a>
                        <button type="button" class="rounded border border-maroon/20 px-2 py-1 text-xs text-maroon hover:bg-maroon/5"
                                hx-get="<?= site_url('admin/dokumen/' . $item->id . '/edit') ?>" hx-target="#dokumen-form-panel" hx-swap="innerHTML">Edit</button>
                        <button type="button" class="rounded border border-red-200 px-2 py-1 text-xs text-red-700 hover:bg-red-50"
                                hx-post="<?= site_url('admin/dokumen/' . $item->id . '/delete') ?>" hx-target="#dokumen-list" hx-swap="outerHTML" hx-confirm="Hapus dokumen ini?">Hapus</button>
                    </div>
                </li>
            <?php endforeach ?>
        </ul>
    <?php endif ?>
</div>
