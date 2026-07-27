<div id="hero-slide-list" class="overflow-hidden rounded-lg border border-gold/20 bg-white shadow-sm">
    <div class="border-b border-gold/10 px-4 py-3">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-stone-600">Daftar Slide</h2>
    </div>

    <?php if ($slides === []): ?>
        <p class="px-4 py-8 text-center text-sm text-stone-500">Belum ada hero slide.</p>
    <?php else: ?>
        <ul class="divide-y divide-stone-100">
            <?php foreach ($slides as $index => $slide): ?>
                <?php $isFirst = $index === 0; ?>
                <?php $isLast = $index === count($slides) - 1; ?>
                <li class="flex items-start gap-3 px-4 py-3">
                    <img src="<?= esc(base_url((string) $slide->gambar)) ?>"
                         alt=""
                         class="h-16 w-24 shrink-0 rounded object-cover bg-stone-100">

                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="font-medium text-maroon"><?= esc($slide->eyebrow ?: 'Tanpa eyebrow') ?></p>
                            <?php if ($slide->is_active): ?>
                                <span class="rounded bg-green-100 px-2 py-0.5 text-xs text-green-800">Aktif</span>
                            <?php else: ?>
                                <span class="rounded bg-stone-100 px-2 py-0.5 text-xs text-stone-600">Nonaktif</span>
                            <?php endif ?>
                            <span class="text-xs text-stone-400">Urutan #<?= esc((string) $slide->urutan) ?></span>
                        </div>
                        <p class="mt-1 whitespace-pre-line text-sm text-stone-700"><?= esc((string) $slide->judul) ?></p>
                        <?php if ($slide->subjudul): ?>
                            <p class="mt-1 line-clamp-2 text-xs text-stone-500"><?= esc((string) $slide->subjudul) ?></p>
                        <?php endif ?>
                    </div>

                    <div class="flex shrink-0 flex-col gap-1">
                        <button type="button"
                                class="rounded border border-stone-200 px-2 py-1 text-xs <?= $isFirst ? 'cursor-not-allowed opacity-40' : 'hover:bg-stone-50' ?>"
                                hx-post="<?= site_url('admin/hero-slide/' . $slide->id . '/move-up') ?>"
                                hx-target="#hero-slide-list"
                                hx-swap="outerHTML"
                                <?= $isFirst ? 'disabled' : '' ?>>
                            ↑
                        </button>
                        <button type="button"
                                class="rounded border border-stone-200 px-2 py-1 text-xs <?= $isLast ? 'cursor-not-allowed opacity-40' : 'hover:bg-stone-50' ?>"
                                hx-post="<?= site_url('admin/hero-slide/' . $slide->id . '/move-down') ?>"
                                hx-target="#hero-slide-list"
                                hx-swap="outerHTML"
                                <?= $isLast ? 'disabled' : '' ?>>
                            ↓
                        </button>
                        <button type="button"
                                class="rounded border border-maroon/20 px-2 py-1 text-xs text-maroon hover:bg-maroon/5"
                                hx-get="<?= site_url('admin/hero-slide/' . $slide->id . '/edit') ?>"
                                hx-target="#hero-slide-form-panel"
                                hx-swap="innerHTML">
                            Edit
                        </button>
                        <button type="button"
                                class="rounded border border-red-200 px-2 py-1 text-xs text-red-700 hover:bg-red-50"
                                hx-post="<?= site_url('admin/hero-slide/' . $slide->id . '/delete') ?>"
                                hx-target="#hero-slide-list"
                                hx-swap="outerHTML"
                                hx-confirm="Hapus slide ini?">
                            Hapus
                        </button>
                    </div>
                </li>
            <?php endforeach ?>
        </ul>
    <?php endif ?>
</div>
