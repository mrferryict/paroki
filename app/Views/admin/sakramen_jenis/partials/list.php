<div id="sakramen-jenis-list" class="overflow-hidden rounded-lg border border-gold/20 bg-white shadow-sm">
    <div class="border-b border-gold/10 px-4 py-3">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-stone-600">Daftar Jenis</h2>
    </div>

    <?php if ($items === []): ?>
        <p class="px-4 py-8 text-center text-sm text-stone-500">Belum ada jenis sakramen/layanan.</p>
    <?php else: ?>
        <ul class="divide-y divide-stone-100">
            <?php foreach ($items as $index => $item): ?>
                <?php $isFirst = $index === 0; ?>
                <?php $isLast = $index === count($items) - 1; ?>
                <li class="flex items-start gap-3 px-4 py-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded bg-gold/20 text-xs font-semibold text-maroon"><?= esc($item->icon) ?></div>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="font-medium text-maroon"><?= esc($item->nama) ?></p>
                            <?php if ($item->is_active): ?>
                                <span class="rounded bg-green-100 px-2 py-0.5 text-xs text-green-800">Aktif</span>
                            <?php else: ?>
                                <span class="rounded bg-stone-100 px-2 py-0.5 text-xs text-stone-600">Nonaktif</span>
                            <?php endif ?>
                            <span class="text-xs text-stone-400"><?= esc($kodeOptions[$item->kode] ?? $item->kode) ?> · #<?= esc((string) $item->urutan) ?></span>
                        </div>
                        <?php if ($item->deskripsi): ?>
                            <p class="mt-1 line-clamp-2 text-sm text-stone-600"><?= esc((string) $item->deskripsi) ?></p>
                        <?php endif ?>
                    </div>
                    <div class="flex shrink-0 flex-col gap-1">
                        <button type="button" class="rounded border border-stone-200 px-2 py-1 text-xs <?= $isFirst ? 'cursor-not-allowed opacity-40' : 'hover:bg-stone-50' ?>"
                                hx-post="<?= site_url('admin/sakramen-jenis/' . $item->id . '/move-up') ?>" hx-target="#sakramen-jenis-list" hx-swap="outerHTML" <?= $isFirst ? 'disabled' : '' ?>>↑</button>
                        <button type="button" class="rounded border border-stone-200 px-2 py-1 text-xs <?= $isLast ? 'cursor-not-allowed opacity-40' : 'hover:bg-stone-50' ?>"
                                hx-post="<?= site_url('admin/sakramen-jenis/' . $item->id . '/move-down') ?>" hx-target="#sakramen-jenis-list" hx-swap="outerHTML" <?= $isLast ? 'disabled' : '' ?>>↓</button>
                        <button type="button" class="rounded border border-maroon/20 px-2 py-1 text-xs text-maroon hover:bg-maroon/5"
                                hx-get="<?= site_url('admin/sakramen-jenis/' . $item->id . '/edit') ?>" hx-target="#sakramen-jenis-form-panel" hx-swap="innerHTML">Edit</button>
                        <button type="button" class="rounded border border-red-200 px-2 py-1 text-xs text-red-700 hover:bg-red-50"
                                hx-post="<?= site_url('admin/sakramen-jenis/' . $item->id . '/delete') ?>" hx-target="#sakramen-jenis-list" hx-swap="outerHTML" hx-confirm="Hapus jenis ini?">Hapus</button>
                    </div>
                </li>
            <?php endforeach ?>
        </ul>
    <?php endif ?>
</div>
