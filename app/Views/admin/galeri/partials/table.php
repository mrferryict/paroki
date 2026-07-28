<?php /** @var list<\App\DTOs\Galeri\GaleriEventAdminRowDto> $rows */ ?>
<div id="galeri-table" class="overflow-hidden rounded-lg border border-gold/20 bg-white shadow-sm" x-data="{ expanded: {} }">
    <div class="border-b border-gold/10 px-4 py-3">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-stone-600">Event Galeri</h2>
    </div>

    <?php if ($rows === []): ?>
        <p class="px-4 py-8 text-center text-sm text-stone-500">Belum ada event galeri.</p>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-ivory/80 text-left text-xs uppercase tracking-wide text-stone-500">
                    <tr>
                        <th class="w-10 px-3 py-3"></th>
                        <th class="px-4 py-3 font-semibold">Judul Event</th>
                        <th class="px-4 py-3 font-semibold">Slug</th>
                        <th class="px-4 py-3 font-semibold">Item</th>
                        <th class="px-4 py-3 font-semibold">Dilihat</th>
                        <th class="px-4 py-3 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    <?php foreach ($rows as $rowIndex => $row): ?>
                        <?php $isFirstEvent = $rowIndex === 0; ?>
                        <?php $isLastEvent = $rowIndex === count($rows) - 1; ?>
                        <tr class="align-top hover:bg-maroon/[0.02]">
                            <td class="px-3 py-3">
                                <button type="button"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded border border-gold/20 text-maroon hover:bg-maroon/5"
                                        @click="expanded[<?= (int) $row->id ?>] = !expanded[<?= (int) $row->id ?>]"
                                        :aria-expanded="expanded[<?= (int) $row->id ?>] || false"
                                        title="Tampilkan item">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform"
                                         :class="expanded[<?= (int) $row->id ?>] ? 'rotate-90' : ''"
                                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                            </td>
                            <td class="px-4 py-3 font-medium text-maroon"><?= esc($row->judul) ?></td>
                            <td class="px-4 py-3 font-mono text-xs text-stone-600"><?= esc($row->slug) ?></td>
                            <td class="px-4 py-3 text-stone-600"><?= esc((string) count($row->items)) ?></td>
                            <td class="px-4 py-3 text-stone-600"><?= esc(number_format($row->viewCount, 0, ',', '.')) ?></td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap justify-end gap-1">
                                    <button type="button"
                                            class="rounded border border-stone-200 px-2 py-1 text-xs <?= $isFirstEvent ? 'cursor-not-allowed opacity-40' : 'hover:bg-stone-50' ?>"
                                            hx-post="<?= site_url('admin/galeri/event/' . $row->id . '/move-up') ?>"
                                            hx-target="#galeri-table" hx-swap="outerHTML"
                                            <?= $isFirstEvent ? 'disabled' : '' ?>>↑</button>
                                    <button type="button"
                                            class="rounded border border-stone-200 px-2 py-1 text-xs <?= $isLastEvent ? 'cursor-not-allowed opacity-40' : 'hover:bg-stone-50' ?>"
                                            hx-post="<?= site_url('admin/galeri/event/' . $row->id . '/move-down') ?>"
                                            hx-target="#galeri-table" hx-swap="outerHTML"
                                            <?= $isLastEvent ? 'disabled' : '' ?>>↓</button>
                                    <button type="button"
                                            class="rounded border border-gold/30 px-2 py-1 text-xs text-maroon hover:bg-maroon/5"
                                            hx-get="<?= site_url('admin/galeri/' . $row->id . '/item/new') ?>"
                                            hx-target="#galeri-form-panel" hx-swap="innerHTML">+ Item</button>
                                    <button type="button"
                                            class="rounded border border-maroon/20 px-2 py-1 text-xs text-maroon hover:bg-maroon/5"
                                            hx-get="<?= site_url('admin/galeri/event/' . $row->id . '/edit') ?>"
                                            hx-target="#galeri-form-panel" hx-swap="innerHTML">Edit</button>
                                    <button type="button"
                                            class="rounded border border-red-200 px-2 py-1 text-xs text-red-700 hover:bg-red-50"
                                            hx-post="<?= site_url('admin/galeri/event/' . $row->id . '/delete') ?>"
                                            hx-target="#galeri-table" hx-swap="outerHTML"
                                            hx-confirm="Hapus event ini beserta seluruh itemnya?">Hapus</button>
                                </div>
                            </td>
                        </tr>
                        <tr x-show="expanded[<?= (int) $row->id ?>]" x-cloak>
                            <td colspan="6" class="bg-ivory/50 px-4 py-4">
                                <?php if ($row->items === []): ?>
                                    <p class="text-sm text-stone-500">Belum ada foto/video di event ini.</p>
                                <?php else: ?>
                                    <div class="overflow-x-auto rounded-lg border border-gold/15 bg-white">
                                        <table class="min-w-full text-sm">
                                            <thead class="bg-stone-50 text-left text-xs uppercase tracking-wide text-stone-500">
                                                <tr>
                                                    <th class="px-4 py-2 font-semibold">Preview</th>
                                                    <th class="px-4 py-2 font-semibold">Jenis</th>
                                                    <th class="px-4 py-2 font-semibold">Caption</th>
                                                    <th class="px-4 py-2 font-semibold text-right">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-stone-100">
                                                <?php foreach ($row->items as $itemIndex => $item): ?>
                                                    <?php $isFirstItem = $itemIndex === 0; ?>
                                                    <?php $isLastItem = $itemIndex === count($row->items) - 1; ?>
                                                    <tr>
                                                        <td class="px-4 py-2">
                                                            <?php if ($item->previewUrl !== null): ?>
                                                                <img src="<?= esc($item->previewUrl) ?>" alt="" class="h-12 w-16 rounded object-cover">
                                                            <?php else: ?>
                                                                <span class="text-xs text-stone-400">—</span>
                                                            <?php endif ?>
                                                        </td>
                                                        <td class="px-4 py-2 text-stone-700"><?= esc($item->jenisLabel) ?></td>
                                                        <td class="px-4 py-2 text-stone-700"><?= esc($item->caption ?: '—') ?></td>
                                                        <td class="px-4 py-2">
                                                            <div class="flex justify-end gap-1">
                                                                <button type="button"
                                                                        class="rounded border border-stone-200 px-2 py-1 text-xs <?= $isFirstItem ? 'cursor-not-allowed opacity-40' : 'hover:bg-stone-50' ?>"
                                                                        hx-post="<?= site_url('admin/galeri/' . $row->id . '/item/' . $item->id . '/move-up') ?>"
                                                                        hx-target="#galeri-table" hx-swap="outerHTML"
                                                                        <?= $isFirstItem ? 'disabled' : '' ?>>↑</button>
                                                                <button type="button"
                                                                        class="rounded border border-stone-200 px-2 py-1 text-xs <?= $isLastItem ? 'cursor-not-allowed opacity-40' : 'hover:bg-stone-50' ?>"
                                                                        hx-post="<?= site_url('admin/galeri/' . $row->id . '/item/' . $item->id . '/move-down') ?>"
                                                                        hx-target="#galeri-table" hx-swap="outerHTML"
                                                                        <?= $isLastItem ? 'disabled' : '' ?>>↓</button>
                                                                <button type="button"
                                                                        class="rounded border border-maroon/20 px-2 py-1 text-xs text-maroon hover:bg-maroon/5"
                                                                        hx-get="<?= site_url('admin/galeri/' . $row->id . '/item/' . $item->id . '/edit') ?>"
                                                                        hx-target="#galeri-form-panel" hx-swap="innerHTML">Edit</button>
                                                                <button type="button"
                                                                        class="rounded border border-red-200 px-2 py-1 text-xs text-red-700 hover:bg-red-50"
                                                                        hx-post="<?= site_url('admin/galeri/' . $row->id . '/item/' . $item->id . '/delete') ?>"
                                                                        hx-target="#galeri-table" hx-swap="outerHTML"
                                                                        hx-confirm="Hapus item ini?">Hapus</button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    <?php endif ?>
</div>
