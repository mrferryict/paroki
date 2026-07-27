<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Admin') ?> — Paroki</title>
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
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/htmx.org@2.0.4"></script>
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <script>
        document.addEventListener('htmx:configRequest', (event) => {
            event.detail.headers['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;
        });
    </script>
</head>
<body class="min-h-screen bg-ivory font-sans text-stone-800">
    <header class="border-b border-gold/30 bg-maroon text-ivory">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4">
            <div>
                <p class="font-display text-2xl font-semibold">Panel Admin Paroki</p>
                <p class="text-sm text-ivory/80">Paroki Hati Kudus Yesus</p>
            </div>
            <div class="flex items-center gap-4 text-sm">
                <a href="<?= site_url('/') ?>" class="hover:text-gold">Lihat Situs</a>
                <a href="<?= site_url('logout') ?>" class="rounded bg-gold/20 px-3 py-1.5 hover:bg-gold/30">Keluar</a>
            </div>
        </div>
    </header>

    <div class="mx-auto flex max-w-6xl gap-6 px-4 py-6">
        <aside class="w-56 shrink-0">
            <nav class="rounded-lg border border-gold/20 bg-white p-3 shadow-sm">
                <p class="mb-2 px-2 text-xs font-semibold uppercase tracking-wide text-stone-500">Konten</p>
                <?php $uri = uri_string(); ?>
                <a href="<?= site_url('admin/hero-slide') ?>"
                   class="block rounded px-3 py-2 text-sm font-medium hover:bg-maroon/5 <?= $uri === 'admin/hero-slide' ? 'bg-maroon/10 text-maroon' : '' ?>">
                    Hero Slide
                </a>
                <a href="<?= site_url('admin/dewan-paroki') ?>"
                   class="block rounded px-3 py-2 text-sm font-medium hover:bg-maroon/5 <?= str_starts_with($uri, 'admin/dewan-paroki') ? 'bg-maroon/10 text-maroon' : '' ?>">
                    Dewan Paroki (DPH)
                </a>
                <a href="<?= site_url('admin/sakramen-jenis') ?>"
                   class="block rounded px-3 py-2 text-sm font-medium hover:bg-maroon/5 <?= str_starts_with($uri, 'admin/sakramen-jenis') ? 'bg-maroon/10 text-maroon' : '' ?>">
                    Sakramen & Layanan
                </a>
                <a href="<?= site_url('admin/jadwal-misa') ?>"
                   class="block rounded px-3 py-2 text-sm font-medium hover:bg-maroon/5 <?= str_starts_with($uri, 'admin/jadwal-misa') ? 'bg-maroon/10 text-maroon' : '' ?>">
                    Jadwal Misa
                </a>
            </nav>
        </aside>

        <main class="min-w-0 flex-1">
            <?= $this->renderSection('content') ?>
        </main>
    </div>
</body>
</html>
