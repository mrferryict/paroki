<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= esc($title ?? 'Paroki Santo Mikael Gombong') ?> — Profil paroki, jadwal misa, layanan, berita, katekese, dan unduhan.">
    <title><?= esc($title ?? 'Paroki Santo Mikael Gombong') ?></title>
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
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.14.8/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/htmx.org@2.0.4"></script>
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <script>
        document.addEventListener('htmx:configRequest', (event) => {
            event.detail.headers['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;
        });
    </script>
    <style>
        [x-cloak] { display: none !important; }
        .hero-slide-enter { animation: heroFade 0.8s ease-out; }
        @keyframes heroFade {
            from { opacity: 0; transform: scale(1.03); }
            to { opacity: 1; transform: scale(1); }
        }
    </style>
    <?= $this->renderSection('head') ?>
</head>
<body class="bg-ivory font-sans text-stone-800 antialiased"
      x-data="landingPage()"
      x-cloak>

    <?= view('partials/site_header', [
        'isHome'     => true,
        'shareUrl'   => current_url(),
        'shareTitle' => $title ?? 'Paroki Santo Mikael Gombong',
    ]) ?>

    <main class="pt-[4.75rem]">
        <?= $this->renderSection('content') ?>
    </main>

    <?= view('partials/icon_scripts') ?>
    <?= view('partials/site_nav_scripts', [
        'shareUrl'   => current_url(),
        'shareTitle' => $title ?? 'Paroki Santo Mikael Gombong',
    ]) ?>

    <?= view('partials/scroll_to_top') ?>

    <script>
        function landingPage() {
            return {
                ...siteNavBase(),
                currentSlide: 0,
                slideTimer: null,
                activeWilayah: null,
                activeKatekeseTab: <?= json_encode($katekeseKategori[0]['value'] ?? 'artikel_iman', JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
                activeLayananTab: <?= json_encode($layananGrup[0]['value'] ?? 'sakramen', JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
                heroSlides: <?= json_encode($heroSlides ?? [], JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
                bidangDPH: <?= json_encode($bidangDPH ?? [], JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
                wilayahList: <?= json_encode($wilayahList ?? [], JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
                layananList: <?= json_encode($layananList ?? [], JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
                layananGrup: <?= json_encode($layananGrup ?? [], JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
                beritaList: <?= json_encode($beritaList ?? [], JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
                katekeseList: <?= json_encode($katekeseList ?? [], JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
                katekeseKategori: <?= json_encode($katekeseKategori ?? [], JSON_HEX_APOS | JSON_HEX_QUOT) ?>,

                init() {
                    this.initScrollTop();

                    if (this.heroSlides.length > 0) {
                        this.startSlideTimer();
                    }
                    if (this.wilayahList.length > 0) {
                        this.activeWilayah = this.wilayahList[0].id;
                    }
                },

                get filteredKatekese() {
                    return this.katekeseList.filter((item) => item.kategori === this.activeKatekeseTab);
                },

                get filteredLayanan() {
                    return this.layananList.filter((item) => item.grup === this.activeLayananTab);
                },

                get currentHero() {
                    return this.heroSlides[this.currentSlide] ?? null;
                },

                startSlideTimer() {
                    this.stopSlideTimer();
                    if (this.heroSlides.length <= 1) {
                        return;
                    }
                    this.slideTimer = setInterval(() => this.nextSlide(), 7000);
                },

                stopSlideTimer() {
                    if (this.slideTimer) {
                        clearInterval(this.slideTimer);
                        this.slideTimer = null;
                    }
                },

                nextSlide() {
                    if (this.heroSlides.length === 0) {
                        return;
                    }
                    this.currentSlide = (this.currentSlide + 1) % this.heroSlides.length;
                },

                prevSlide() {
                    if (this.heroSlides.length === 0) {
                        return;
                    }
                    this.currentSlide = (this.currentSlide - 1 + this.heroSlides.length) % this.heroSlides.length;
                },

                goToSlide(index) {
                    this.currentSlide = index;
                    this.startSlideTimer();
                },

                toggleWilayah(id) {
                    this.activeWilayah = this.activeWilayah === id ? null : id;
                },

                iconSvg(name) {
                    return window.PAROKI_ICONS[name] || window.PAROKI_ICONS.default;
                },

                scrollToForm(sakramenId) {
                    const select = document.getElementById('sakramen_jenis_id');
                    if (select && sakramenId) {
                        select.value = String(sakramenId);
                    }
                    document.getElementById('pendaftaran-layanan')?.scrollIntoView({ behavior: 'smooth' });
                },
            };
        }
    </script>
</body>
</html>
