<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?> — Paroki</title>
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
    <?= $this->renderSection('pageStyles') ?>
</head>
<body class="min-h-screen bg-ivory font-sans text-stone-800 antialiased">
    <main class="flex min-h-screen items-center justify-center px-4 py-10">
        <?= $this->renderSection('main') ?>
    </main>
    <?= $this->renderSection('pageScripts') ?>
</body>
</html>
