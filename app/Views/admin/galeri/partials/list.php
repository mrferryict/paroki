<div id="galeri-list" class="overflow-hidden rounded-lg border border-gold/20 bg-white shadow-sm">
    <div class="border-b border-gold/10 px-4 py-3">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-stone-600">Daftar Foto</h2>
    </div>

    <?php if ($items === []): ?>
        <p class="px-4 py-8 text-center text-sm text-stone-500">Belum ada foto galeri.</p>
    <?php else: ?>
        <ul class="divide-y divide-stone-100">
            <?php foreach ($items as $index => $item): ?>
                <?php $isFirst = $index === 0; ?>
                <?php $isLast = $index === count($items) - 1; ?>
                <li class="flex items-start gap-3 px-4 py-3">
                    <img src="<?= esc(base_url(ltrim((string) $item->file_path, '/'))) ?>" alt="" class="h-16 w-16 shrink-0 rounded object-cover">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-maroon"><?= esc($item->caption ?: 'Tanpa caption') ?></p>
                        <p class="mt-1 text-xs text-stone-400">Urutan #<?= esc((string) $item->urutan) ?></p>
                    </div>
                    <div class="flex shrink-0 flex-col gap-1">
                        <button type="button" class="rounded border border-stone-200 px-2 py-1 text-xs <?= $isFirst ? 'cursor-not-allowed opacity-40' : 'hover:bg-stone-50' ?>"
                                hx-post="<?= site_url('admin/galeri/' . $item->id . '/move-up') ?>" hx-target="#galeri-list" hx-swap="outerHTML" <?= $isFirst ? 'disabled' : '' ?>>↑</button>
                        <button type="button" class="rounded border border-stone-200 px-2 py-1 text-xs <?= $isLast ? 'cursor-not-allowed opacity-40' : 'hover:bg-stone-50' ?>"
                                hx-post="<?= site_url('admin/galeri/' . $item->id . '/move-down') ?>" hx-target="#galeri-list" hx-swap="outerHTML" <?= $isLast ? 'disabled' : '' ?>>↓</button>
                        <button type="button" class="rounded border border-maroon/20 px-2 py-1 text-xs text-maroon hover:bg-maroon/5"
                                hx-get="<?= site_url('admin/galeri/' . $item->id . '/edit') ?>" hx-target="#galeri-form-panel" hx-swap="innerHTML">Edit</button>
                        <button type="button" class="rounded border border-red-200 px-2 py-1 text-xs text-red-700 hover:bg-red-50"
                                hx-post="<?= site_url('admin/galeri/' . $item->id . '/delete') ?>" hx-target="#galeri-list" hx-swap="outerHTML" hx-confirm="Hapus foto ini?">Hapus</button>
                    </div>
                </li>
            <?php endforeach ?>
        </ul>
    <?php endif ?>
</div>
