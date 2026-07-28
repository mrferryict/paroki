<?php /** @var list<\App\DTOs\Wilayah\WilayahAdminRowDto> $rows */ ?>
<div id="wilayah-table" class="overflow-hidden rounded-lg border border-gold/20 bg-white shadow-sm" x-data="{ expanded: {} }">
    <div class="border-b border-gold/10 px-4 py-3">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-stone-600">Wilayah & Lingkungan</h2>
    </div>

    <?php if ($rows === []): ?>
        <p class="px-4 py-8 text-center text-sm text-stone-500">Belum ada wilayah.</p>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-ivory/80 text-left text-xs uppercase tracking-wide text-stone-500">
                    <tr>
                        <th class="w-10 px-3 py-3"></th>
                        <th class="px-4 py-3 font-semibold">Nama Wilayah</th>
                        <th class="px-4 py-3 font-semibold">Koordinator</th>
                        <th class="px-4 py-3 font-semibold">No. WA</th>
                        <th class="px-4 py-3 font-semibold">Lingkungan</th>
                        <th class="px-4 py-3 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    <?php foreach ($rows as $row): ?>
                        <tr class="align-top hover:bg-maroon/[0.02]">
                            <td class="px-3 py-3">
                                <button type="button"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded border border-gold/20 text-maroon hover:bg-maroon/5"
                                        @click="expanded[<?= (int) $row->id ?>] = !expanded[<?= (int) $row->id ?>]"
                                        :aria-expanded="expanded[<?= (int) $row->id ?>] || false"
                                        title="Tampilkan lingkungan">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform"
                                         :class="expanded[<?= (int) $row->id ?>] ? 'rotate-90' : ''"
                                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                            </td>
                            <td class="px-4 py-3 font-medium text-maroon"><?= esc($row->nama) ?></td>
                            <td class="px-4 py-3 text-stone-700"><?= esc($row->koordinatorNama) ?></td>
                            <td class="px-4 py-3 font-mono text-stone-700"><?= esc($row->koordinatorKontak) ?></td>
                            <td class="px-4 py-3 text-stone-600"><?= esc((string) count($row->lingkungan)) ?></td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap justify-end gap-1">
                                    <button type="button"
                                            class="rounded border border-gold/30 px-2 py-1 text-xs text-maroon hover:bg-maroon/5"
                                            hx-get="<?= site_url('admin/wilayah/' . $row->id . '/lingkungan/new') ?>"
                                            hx-target="#wilayah-form-panel"
                                            hx-swap="innerHTML">+ Lingkungan</button>
                                    <button type="button"
                                            class="rounded border border-maroon/20 px-2 py-1 text-xs text-maroon hover:bg-maroon/5"
                                            hx-get="<?= site_url('admin/wilayah/' . $row->id . '/edit') ?>"
                                            hx-target="#wilayah-form-panel"
                                            hx-swap="innerHTML">Edit</button>
                                    <button type="button"
                                            class="rounded border border-red-200 px-2 py-1 text-xs text-red-700 hover:bg-red-50"
                                            hx-post="<?= site_url('admin/wilayah/' . $row->id . '/delete') ?>"
                                            hx-target="#wilayah-table"
                                            hx-swap="outerHTML"
                                            hx-confirm="Hapus wilayah ini beserta data terkait?">Hapus</button>
                                </div>
                            </td>
                        </tr>
                        <tr x-show="expanded[<?= (int) $row->id ?>]" x-cloak>
                            <td colspan="6" class="bg-ivory/50 px-4 py-4">
                                <?php if ($row->lingkungan === []): ?>
                                    <p class="text-sm text-stone-500">Belum ada lingkungan di wilayah ini.</p>
                                <?php else: ?>
                                    <div class="overflow-x-auto rounded-lg border border-gold/15 bg-white">
                                        <table class="min-w-full text-sm">
                                            <thead class="bg-stone-50 text-left text-xs uppercase tracking-wide text-stone-500">
                                                <tr>
                                                    <th class="px-4 py-2 font-semibold">Nama Lingkungan</th>
                                                    <th class="px-4 py-2 font-semibold">Ketua</th>
                                                    <th class="px-4 py-2 font-semibold">No. WA</th>
                                                    <th class="px-4 py-2 font-semibold text-right">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-stone-100">
                                                <?php foreach ($row->lingkungan as $lingkungan): ?>
                                                    <tr>
                                                        <td class="px-4 py-2 font-medium text-stone-800"><?= esc($lingkungan->nama) ?></td>
                                                        <td class="px-4 py-2 text-stone-700"><?= esc($lingkungan->ketuaNama) ?></td>
                                                        <td class="px-4 py-2 font-mono text-stone-700">
                                                            <?= $lingkungan->ketuaKontak !== null && $lingkungan->ketuaKontak !== '' ? esc($lingkungan->ketuaKontak) : '—' ?>
                                                        </td>
                                                        <td class="px-4 py-2">
                                                            <div class="flex justify-end gap-1">
                                                                <button type="button"
                                                                        class="rounded border border-maroon/20 px-2 py-1 text-xs text-maroon hover:bg-maroon/5"
                                                                        hx-get="<?= site_url('admin/wilayah/' . $row->id . '/lingkungan/' . $lingkungan->id . '/edit') ?>"
                                                                        hx-target="#wilayah-form-panel"
                                                                        hx-swap="innerHTML">Edit</button>
                                                                <button type="button"
                                                                        class="rounded border border-red-200 px-2 py-1 text-xs text-red-700 hover:bg-red-50"
                                                                        hx-post="<?= site_url('admin/wilayah/' . $row->id . '/lingkungan/' . $lingkungan->id . '/delete') ?>"
                                                                        hx-target="#wilayah-table"
                                                                        hx-swap="outerHTML"
                                                                        hx-confirm="Hapus lingkungan ini?">Hapus</button>
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
