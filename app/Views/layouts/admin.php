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
    <style>[x-cloak] { display: none !important; }</style>
    <?= $this->renderSection('head') ?>
</head>
<body class="min-h-screen bg-ivory font-sans text-stone-800" x-data="{ sidebarOpen: false }" x-cloak>
    <header class="sticky top-0 z-40 border-b border-gold/30 bg-maroon text-ivory">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 lg:px-6">
            <div class="flex items-center gap-3">
                <button type="button"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-gold/10 lg:hidden"
                        @click="sidebarOpen = !sidebarOpen"
                        :aria-expanded="sidebarOpen"
                        aria-label="Menu navigasi">
                    <svg x-show="!sidebarOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg x-show="sidebarOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
                <div>
                    <p class="font-display text-xl font-semibold sm:text-2xl">Panel Admin Paroki</p>
                    <p class="text-xs text-ivory/80 sm:text-sm">Paroki Hati Kudus Yesus</p>
                </div>
            </div>
            <div class="flex items-center gap-3 text-sm">
                <?php if (auth()->loggedIn()): ?>
                    <span class="hidden text-ivory/80 sm:inline"><?= esc(auth()->user()->username ?? auth()->user()->email ?? 'Admin') ?></span>
                <?php endif ?>
                <a href="<?= site_url('/') ?>" class="rounded px-2 py-1 hover:bg-gold/10 hover:text-gold">Lihat Situs</a>
                <a href="<?= site_url('logout') ?>" class="rounded bg-gold/20 px-3 py-1.5 hover:bg-gold/30">Keluar</a>
            </div>
        </div>
    </header>

    <div class="mx-auto flex max-w-7xl gap-6 px-4 py-6 lg:px-6">
        <aside class="hidden w-60 shrink-0 lg:block">
            <?php $uri = uri_string(); ?>
            <?= view('admin/partials/sidebar', ['uri' => $uri]) ?>
        </aside>

        <div x-show="sidebarOpen"
             x-transition
             @click.outside="sidebarOpen = false"
             class="fixed inset-0 z-30 bg-stone-900/40 lg:hidden"
             x-cloak>
            <aside class="h-full w-72 overflow-y-auto bg-ivory p-4 shadow-xl" @click.stop>
                <?php $uri = uri_string(); ?>
                <?= view('admin/partials/sidebar', ['uri' => $uri]) ?>
            </aside>
        </div>

        <main class="min-w-0 flex-1">
            <?= $this->renderSection('content') ?>
        </main>
    </div>
</body>
</html>
