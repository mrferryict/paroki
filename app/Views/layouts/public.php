<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= esc($metaDescription ?? ($title ?? 'Paroki Santo Mikael Gombong')) ?>">
    <title><?= esc($title ?? 'Paroki Santo Mikael Gombong') ?> — Paroki Santo Mikael Gombong</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        maroon: '#722F37',
                        gold: '#C5A572',
                        ivory: '#FAF7F2',
                    },
                    fontFamily: {
                        display: ['"Playfair Display"', 'serif'],
                        sans: ['"Outfit"', 'sans-serif'],
                    },
                },
            },
        };
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
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
    <?= view('partials/site_nav_scripts', [
        'shareUrl'   => $shareUrl ?? current_url(),
        'shareTitle' => $shareTitle ?? ($title ?? 'Paroki Santo Mikael Gombong'),
    ]) ?>
</body>
</html>
