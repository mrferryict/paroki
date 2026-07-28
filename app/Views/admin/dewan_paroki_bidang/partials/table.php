<?php /** @var list<\App\DTOs\DewanParokiBidang\DewanParokiBidangAdminRowDto> $rows */ ?>
<div id="dewan-paroki-table" class="overflow-hidden rounded-lg border border-gold/20 bg-white shadow-sm" x-data="{ expanded: {} }">
    <div class="border-b border-gold/10 px-4 py-3">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-stone-600">Bidang DPH & Penjabat</h2>
    </div>

    <?php if ($rows === []): ?>
        <p class="px-4 py-8 text-center text-sm text-stone-500">Belum ada bidang DPH.</p>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-ivory/80 text-left text-xs uppercase tracking-wide text-stone-500">
                    <tr>
                        <th class="w-10 px-3 py-3"></th>
                        <th class="px-4 py-3 font-semibold">Bidang</th>
                        <th class="px-4 py-3 font-semibold">Kode</th>
                        <th class="px-4 py-3 font-semibold">Penjabat</th>
                        <th class="px-4 py-3 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    <?php foreach ($rows as $row): ?>
                        <tr class="align-top hover:bg-maroon/[0.02]">
                            <td class="px-3 py-3">
                                <button type="button"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded border border-gold/20 text-maroon hover:bg-maroon/5"
                                        @click="expanded[<?= (int) $row->id ?>] = !expanded[<?= (int) $row->id ?>]">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform"
                                         :class="expanded[<?= (int) $row->id ?>] ? 'rotate-90' : ''"
                                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-medium text-maroon"><?= esc($row->nama) ?></p>
                                <?php if ($row->deskripsi): ?>
                                    <p class="mt-1 line-clamp-2 text-xs text-stone-500"><?= esc($row->deskripsi) ?></p>
                                <?php endif ?>
                            </td>
                            <td class="px-4 py-3 text-stone-600"><?= esc($row->kodeLabel) ?></td>
                            <td class="px-4 py-3 text-stone-600"><?= esc((string) count($row->penjabat)) ?></td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap justify-end gap-1">
                                    <button type="button"
                                            class="rounded border border-gold/30 px-2 py-1 text-xs text-maroon hover:bg-maroon/5"
                                            hx-get="<?= site_url('admin/dewan-paroki/' . $row->id . '/penjabat/new') ?>"
                                            hx-target="#dewan-paroki-form-panel"
                                            hx-swap="innerHTML">+ Penjabat</button>
                                    <button type="button"
                                            class="rounded border border-maroon/20 px-2 py-1 text-xs text-maroon hover:bg-maroon/5"
                                            hx-get="<?= site_url('admin/dewan-paroki/' . $row->id . '/edit') ?>"
                                            hx-target="#dewan-paroki-form-panel"
                                            hx-swap="innerHTML">Edit</button>
                                    <button type="button"
                                            class="rounded border border-red-200 px-2 py-1 text-xs text-red-700 hover:bg-red-50"
                                            hx-post="<?= site_url('admin/dewan-paroki/' . $row->id . '/delete') ?>"
                                            hx-target="#dewan-paroki-table"
                                            hx-swap="outerHTML"
                                            hx-confirm="Hapus bidang ini?">Hapus</button>
                                </div>
                            </td>
                        </tr>
                        <tr x-show="expanded[<?= (int) $row->id ?>]" x-cloak>
                            <td colspan="5" class="bg-ivory/50 px-4 py-4">
                                <?php if ($row->penjabat === []): ?>
                                    <p class="text-sm text-stone-500">Belum ada penjabat untuk bidang ini.</p>
                                <?php else: ?>
                                    <table class="min-w-full overflow-hidden rounded-lg border border-gold/15 bg-white text-sm">
                                        <thead class="bg-stone-50 text-left text-xs uppercase tracking-wide text-stone-500">
                                            <tr>
                                                <th class="px-4 py-2 font-semibold">Nama</th>
                                                <th class="px-4 py-2 font-semibold">No. WA</th>
                                                <th class="px-4 py-2 font-semibold text-right">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-stone-100">
                                            <?php foreach ($row->penjabat as $penjabat): ?>
                                                <tr>
                                                    <td class="px-4 py-2 font-medium text-stone-800"><?= esc($penjabat->nama) ?></td>
                                                    <td class="px-4 py-2 font-mono text-stone-700"><?= esc($penjabat->whatsapp) ?></td>
                                                    <td class="px-4 py-2">
                                                        <div class="flex justify-end gap-1">
                                                            <button type="button"
                                                                    class="rounded border border-maroon/20 px-2 py-1 text-xs text-maroon hover:bg-maroon/5"
                                                                    hx-get="<?= site_url('admin/dewan-paroki/' . $row->id . '/penjabat/' . $penjabat->id . '/edit') ?>"
                                                                    hx-target="#dewan-paroki-form-panel"
                                                                    hx-swap="innerHTML">Edit</button>
                                                            <button type="button"
                                                                    class="rounded border border-red-200 px-2 py-1 text-xs text-red-700 hover:bg-red-50"
                                                                    hx-post="<?= site_url('admin/dewan-paroki/' . $row->id . '/penjabat/' . $penjabat->id . '/delete') ?>"
                                                                    hx-target="#dewan-paroki-table"
                                                                    hx-swap="outerHTML"
                                                                    hx-confirm="Hapus penjabat ini?">Hapus</button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach ?>
                                        </tbody>
                                    </table>
                                <?php endif ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    <?php endif ?>
</div>
