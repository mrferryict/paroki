<?php /** @var \App\DTOs\Shared\PaginatedResultDto $result */ ?>
<?php /** @var \App\DTOs\Shared\ContentListFilterDto $filter */ ?>
<?php /** @var string|null $activeKategori */ ?>
<?php
$listUrl = $activeKategori
    ? site_url('admin/artikel/kategori/' . $activeKategori)
    : site_url('admin/artikel');
?>
<div id="artikel-list" class="overflow-hidden rounded-lg border border-gold/20 bg-white shadow-sm">
    <div class="border-b border-gold/10 px-4 py-3">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-stone-600">Daftar Artikel</h2>
    </div>

    <?php if ($result->items === []): ?>
        <p class="px-4 py-8 text-center text-sm text-stone-500">Belum ada artikel.</p>
    <?php else: ?>
        <ul class="divide-y divide-stone-100">
            <?php foreach ($result->items as $item): ?>
                <?php
                $kategoriValue = $item->kategori instanceof \App\Enums\ArtikelKategori ? $item->kategori->value : (string) $item->kategori;
                $statusValue   = $item->status instanceof \App\Enums\PublishStatus ? $item->status->value : (string) $item->status;
                ?>
                <li class="flex items-start gap-3 px-4 py-3">
                    <div class="min-w-0 flex-1">
                        <p class="font-medium text-maroon"><?= esc($item->judul) ?></p>
                        <p class="mt-1 text-xs text-stone-500">
                            <?= esc($kategoriOptions[$kategoriValue] ?? $kategoriValue) ?>
                            · <?= esc($statusOptions[$statusValue] ?? $statusValue) ?>
                            <?php if ($item->tanggal_terbit): ?>
                                · <?= esc($item->tanggal_terbit->toLocalizedString('d MMM yyyy')) ?>
                            <?php endif ?>
                        </p>
                    </div>
                    <div class="flex shrink-0 flex-col gap-1">
                        <button type="button" class="rounded border border-maroon/20 px-2 py-1 text-xs text-maroon hover:bg-maroon/5"
                                hx-get="<?= site_url('admin/artikel/' . $item->id . '/edit') ?>" hx-target="#artikel-form-panel" hx-swap="innerHTML">Edit</button>
                        <button type="button" class="rounded border border-red-200 px-2 py-1 text-xs text-red-700 hover:bg-red-50"
                                hx-post="<?= site_url('admin/artikel/' . $item->id . '/delete') ?>?<?= http_build_query(array_filter(['kategori' => $filter->kategori, 'status' => $filter->status, 'page' => $filter->page])) ?>"
                                hx-target="#artikel-list" hx-swap="outerHTML" hx-confirm="Hapus artikel ini?">Hapus</button>
                    </div>
                </li>
            <?php endforeach ?>
        </ul>
        <?= view('admin/partials/pagination_htmx', [
            'result'   => $result,
            'filter'   => $filter,
            'listUrl'  => $listUrl,
            'targetId' => 'artikel-list',
        ]) ?>
    <?php endif ?>
</div>
