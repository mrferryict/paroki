<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= esc($metaDescription ?? ($title ?? 'Paroki Santo Mikael Gombong')) ?>">
    <title><?= esc($title ?? 'Paroki Santo Mikael Gombong') ?> — Paroki Santo Mikael Gombong</title>
    <?= view('partials/site_head') ?>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <?= $this->renderSection('head') ?>
</head>
<body class="bg-ivory font-sans text-stone-800 antialiased"
      x-data="siteNavBase()"
      x-cloak>

    <?= view('partials/site_header', [
        'isHome'     => false,
        'shareUrl'   => $shareUrl ?? current_url(),
        'shareTitle' => $shareTitle ?? ($title ?? 'Paroki Santo Mikael Gombong'),
    ]) ?>

    <main class="min-h-[60vh] pt-[4.75rem]">
        <?= $this->renderSection('content') ?>
    </main>

    <?= view('partials/footer') ?>
    <?= view('partials/scroll_to_top') ?>
    <?= view('partials/site_nav_scripts', [
        'shareUrl'   => $shareUrl ?? current_url(),
        'shareTitle' => $shareTitle ?? ($title ?? 'Paroki Santo Mikael Gombong'),
    ]) ?>
</body>
</html>
