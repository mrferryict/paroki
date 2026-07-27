<?php /** @var object $result — must expose ->pager */ ?>
<?php /** @var \App\DTOs\Shared\ContentListFilterDto|null $filter */ ?>
<?php /** @var array<string, mixed>|null $queryParams */ ?>
<?php /** @var string $listUrl */ ?>
<?php
$query = isset($queryParams)
    ? array_filter($queryParams, static fn ($v) => $v !== null && $v !== '')
    : array_filter([
        'kategori' => $filter->kategori ?? null,
        'status'   => $filter->status ?? null,
    ], static fn ($v) => $v !== null && $v !== '');

$buildPageUrl = static function (int $page) use ($listUrl, $query): string {
    return $listUrl . '?' . http_build_query(array_merge($query, ['page' => $page]));
};

$pager       = $result->pager;
$pageCount   = $pager->getPageCount();
$currentPage = $pager->getCurrentPage();
$total       = (int) ($pager->getDetails()['total'] ?? 0);
$prevPage    = $currentPage > 1 ? $currentPage - 1 : null;
$nextPage    = $currentPage < $pageCount ? $currentPage + 1 : null;
?>
<?php if ($pageCount > 1): ?>
    <nav class="flex flex-wrap items-center justify-between gap-2 border-t border-stone-100 px-4 py-3 text-sm">
        <p class="text-stone-500">
            Halaman <?= esc((string) $currentPage) ?> dari <?= esc((string) $pageCount) ?>
            (<?= esc((string) $total) ?> item)
        </p>
        <div class="flex gap-1">
            <?php if ($prevPage !== null): ?>
                <a href="<?= esc($buildPageUrl($prevPage)) ?>"
                   hx-get="<?= esc($buildPageUrl($prevPage)) ?>"
                   hx-target="#<?= esc($targetId) ?>" hx-swap="outerHTML"
                   class="rounded border border-stone-200 px-3 py-1 hover:bg-stone-50">&larr;</a>
            <?php endif ?>
            <?php if ($nextPage !== null): ?>
                <a href="<?= esc($buildPageUrl($nextPage)) ?>"
                   hx-get="<?= esc($buildPageUrl($nextPage)) ?>"
                   hx-target="#<?= esc($targetId) ?>" hx-swap="outerHTML"
                   class="rounded border border-stone-200 px-3 py-1 hover:bg-stone-50">&rarr;</a>
            <?php endif ?>
        </div>
    </nav>
<?php endif ?>
