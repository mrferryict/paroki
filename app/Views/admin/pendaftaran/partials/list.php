<?php /** @var \App\DTOs\Pendaftaran\PaginatedPendaftaranListDto $result */ ?>
<?php /** @var \App\DTOs\Pendaftaran\PendaftaranListFilterDto $filter */ ?>
<div id="pendaftaran-list" class="overflow-hidden rounded-lg border border-gold/20 bg-white shadow-sm">
    <div class="border-b border-gold/10 px-4 py-3">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-stone-600">Daftar Pendaftaran</h2>
    </div>

    <?php if ($result->items === []): ?>
        <p class="px-4 py-8 text-center text-sm text-stone-500">Belum ada pendaftaran.</p>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-stone-50 text-left text-xs uppercase tracking-wide text-stone-500">
                    <tr>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Jenis Layanan</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    <?php foreach ($result->items as $item): ?>
                        <tr>
                            <td class="px-4 py-3 font-medium text-maroon"><?= esc($item->namaLengkap) ?></td>
                            <td class="px-4 py-3 text-stone-600"><?= esc($item->sakramenNama ?? '—') ?></td>
                            <td class="px-4 py-3">
                                <span class="rounded bg-stone-100 px-2 py-0.5 text-xs"><?= esc($statusOptions[$item->status->value] ?? $item->status->value) ?></span>
                            </td>
                            <td class="px-4 py-3 text-stone-600"><?= esc($item->createdAt->toLocalizedString('d MMM yyyy HH:mm')) ?></td>
                            <td class="px-4 py-3 text-right">
                                <a href="<?= site_url('admin/pendaftaran/' . $item->id) ?>" class="text-xs text-maroon hover:underline">Detail</a>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
        <?= view('admin/partials/pagination_htmx', [
            'result'      => $result,
            'listUrl'     => site_url('admin/pendaftaran'),
            'targetId'    => 'pendaftaran-list',
            'queryParams' => array_filter(['status' => $filter->status]),
        ]) ?>
    <?php endif ?>
</div>
