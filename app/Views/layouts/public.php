<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= esc($metaDescription ?? ($title ?? 'Paroki Hati Kudus Yesus')) ?>">
    <title><?= esc($title ?? 'Paroki Hati Kudus Yesus') ?> — Paroki Hati Kudus Yesus</title>
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
                        display: ['"Cormorant Garamond"', 'serif'],
                        sans: ['"Work Sans"', 'sans-serif'],
                    },
                },
            },
        };
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <?= $this->renderSection('head') ?>
</head>
<body class="bg-ivory font-sans text-stone-800 antialiased">

    <header class="border-b border-gold/20 bg-maroon text-ivory">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4 sm:px-6">
            <a href="<?= site_url('/') ?>" class="font-display text-xl font-semibold tracking-tight sm:text-2xl hover:text-gold transition-colors">
                Paroki Hati Kudus Yesus
            </a>
            <nav class="hidden items-center gap-5 text-sm font-medium md:flex">
                <a href="<?= site_url('/') ?>#profil" class="hover:text-gold transition-colors">Profil</a>
                <a href="<?= site_url('berita') ?>" class="hover:text-gold transition-colors">Berita</a>
                <a href="<?= site_url('katekese') ?>" class="hover:text-gold transition-colors">Katekese</a>
                <a href="<?= site_url('/') ?>#formulir" class="rounded bg-gold/20 px-4 py-2 hover:bg-gold/30 transition-colors">Formulir</a>
            </nav>
            <a href="<?= site_url('/') ?>" class="text-sm font-medium text-gold hover:text-ivory md:hidden">← Beranda</a>
        </div>
    </header>

    <main class="min-h-[60vh]">
        <?= $this->renderSection('content') ?>
    </main>

    <?= view('partials/public_footer') ?>
</body>
</html>
