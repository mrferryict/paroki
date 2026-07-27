<?php /** @var CodeIgniter\Pager\PagerInterface $pager */ ?>
<?php /** @var array<string, mixed> $queryParams */ ?>
<?php /** @var string|null $paginationBaseUrl */ ?>
<?php
$query = array_filter($queryParams ?? [], static fn ($value) => $value !== null && $value !== '');

$buildPageUrl = static function (int $page) use ($query, $paginationBaseUrl): string {
    $queryString = http_build_query(array_merge($query, ['page' => $page]));

    if (isset($paginationBaseUrl) && $paginationBaseUrl !== '') {
        return $paginationBaseUrl . ($queryString !== '' ? '?' . $queryString : '');
    }

    return '?' . $queryString;
};

$pageCount   = $pager->getPageCount();
$currentPage = $pager->getCurrentPage();
$total       = (int) ($pager->getDetails()['total'] ?? 0);
$prevPage    = $currentPage > 1 ? $currentPage - 1 : null;
$nextPage    = $currentPage < $pageCount ? $currentPage + 1 : null;
?>
<?php if ($pageCount > 1): ?>
    <nav class="mt-12 flex flex-wrap items-center justify-between gap-4 border-t border-gold/20 pt-8">
        <p class="text-sm text-stone-500">
            Halaman <?= esc((string) $currentPage) ?> dari <?= esc((string) $pageCount) ?>
            (<?= esc((string) $total) ?> <?= esc($paginationLabel ?? 'item') ?>)
        </p>
        <div class="flex gap-2">
            <?php if ($prevPage !== null): ?>
                <a href="<?= esc($buildPageUrl($prevPage)) ?>"
                   class="rounded-lg border border-gold/30 px-4 py-2 text-sm font-medium text-maroon hover:bg-maroon/5 transition-colors">
                    &larr; Sebelumnya
                </a>
            <?php endif ?>
            <?php if ($nextPage !== null): ?>
                <a href="<?= esc($buildPageUrl($nextPage)) ?>"
                   class="rounded-lg border border-gold/30 px-4 py-2 text-sm font-medium text-maroon hover:bg-maroon/5 transition-colors">
                    Selanjutnya &rarr;
                </a>
            <?php endif ?>
        </div>
    </nav>
<?php endif ?>
