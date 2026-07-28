<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<section class="py-12 lg:py-16">
    <div class="mx-auto max-w-6xl px-4 sm:px-6">
        <nav class="text-sm text-stone-500">
            <a href="<?= site_url('/') ?>" class="hover:text-maroon transition-colors">Beranda</a>
            <span class="mx-2">/</span>
            <span class="text-maroon">Berita & Kegiatan</span>
        </nav>

        <div class="mt-6 flex flex-wrap items-end justify-between gap-4">
            <div>
                <span class="inline-block h-1 w-12 bg-gold"></span>
                <h1 class="mt-4 font-display text-3xl font-semibold text-maroon lg:text-4xl">Berita & Kegiatan</h1>
                <p class="mt-3 text-stone-600">Arsip pengumuman dan kegiatan paroki.</p>
            </div>
            <a href="<?= site_url('/') ?>#berita"
               class="text-sm font-semibold text-maroon hover:text-gold transition-colors">
                ← Kembali ke beranda
            </a>
        </div>

        <?php
        $buildBeritaUrl = static function (?string $kategori = null, ?string $tag = null): string {
            $params = array_filter([
                'kategori' => $kategori,
                'tag'      => $tag,
            ], static fn ($value) => $value !== null && $value !== '');

            $query = http_build_query($params);

            return site_url('berita') . ($query !== '' ? '?' . $query : '');
        };
        $allUrl      = $buildBeritaUrl(tag: ($activeTag ?? '') !== '' ? $activeTag : null);
        $isAllActive = ($activeKategori ?? '') === '';
        ?>

        <div class="mt-8 flex flex-wrap gap-2">
            <a href="<?= esc($allUrl) ?>"
               class="rounded-full px-4 py-2 text-sm font-medium transition <?= $isAllActive ? 'bg-maroon text-ivory' : 'bg-white text-stone-600 ring-1 ring-gold/30 hover:bg-maroon/5' ?>">
                Semua
            </a>
            <?php foreach ($kategoriOptions as $value => $label): ?>
                <?php $isActive = ($activeKategori ?? '') === $value; ?>
                <a href="<?= esc($buildBeritaUrl(kategori: $value, tag: ($activeTag ?? '') !== '' ? $activeTag : null)) ?>"
                   class="rounded-full px-4 py-2 text-sm font-medium transition <?= $isActive ? 'bg-maroon text-ivory' : 'bg-white text-stone-600 ring-1 ring-gold/30 hover:bg-maroon/5' ?>">
                    <?= esc($label) ?>
                </a>
            <?php endforeach ?>
        </div>

        <form method="get" action="<?= site_url('berita') ?>" class="mt-6 flex flex-wrap items-end gap-3">
            <?php if (($activeKategori ?? '') !== ''): ?>
                <input type="hidden" name="kategori" value="<?= esc($activeKategori) ?>">
            <?php endif ?>
            <div class="min-w-[12rem] flex-1">
                <label for="tag" class="mb-1 block text-sm font-medium text-stone-700">Cari berdasarkan tag</label>
                <input type="text" name="tag" id="tag" value="<?= esc($activeTag ?? '') ?>"
                       placeholder="Mis. natal, kegiatan"
                       class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-maroon focus:outline-none focus:ring-1 focus:ring-maroon">
            </div>
            <button type="submit"
                    class="rounded-lg bg-maroon px-4 py-2.5 text-sm font-semibold text-ivory hover:bg-maroon/90 transition-colors">
                Cari
            </button>
            <?php if (($activeTag ?? '') !== ''): ?>
                <a href="<?= esc($buildBeritaUrl(kategori: ($activeKategori ?? '') !== '' ? $activeKategori : null)) ?>"
                   class="rounded-lg border border-gold/30 px-4 py-2.5 text-sm font-medium text-stone-600 hover:bg-white transition-colors">
                    Reset tag
                </a>
            <?php endif ?>
        </form>

        <?php if (($tagOptions ?? []) !== []): ?>
            <div class="mt-4 flex flex-wrap items-center gap-2">
                <span class="text-xs font-semibold uppercase tracking-wide text-stone-500">Tag populer:</span>
                <?php foreach ($tagOptions as $tagOption): ?>
                    <?php $isTagActive = ($activeTag ?? '') === $tagOption; ?>
                    <a href="<?= esc($buildBeritaUrl(
                        kategori: ($activeKategori ?? '') !== '' ? $activeKategori : null,
                        tag: $tagOption,
                    )) ?>"
                       class="rounded-full px-3 py-1 text-xs font-medium transition <?= $isTagActive ? 'bg-gold/30 text-maroon' : 'bg-white text-stone-600 ring-1 ring-gold/20 hover:bg-gold/10' ?>">
                        #<?= esc($tagOption) ?>
                    </a>
                <?php endforeach ?>
            </div>
        <?php endif ?>

        <?php if ($items === []): ?>
            <div class="mt-12 rounded-2xl border border-gold/20 bg-white px-6 py-12 text-center">
                <p class="text-stone-500">Belum ada berita terbit<?= ($activeKategori ?? '') !== '' ? ' untuk kategori ini' : '' ?><?= ($activeTag ?? '') !== '' ? ' dengan tag tersebut' : '' ?>.</p>
                <p class="mt-2 text-sm text-stone-400">Konten akan muncul otomatis setelah dipublikasikan dari panel admin.</p>
            </div>
        <?php else: ?>
            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <?php foreach ($items as $item): ?>
                    <article class="group overflow-hidden rounded-2xl border border-gold/20 bg-white shadow-sm transition hover:shadow-md">
                        <a href="<?= esc($item['href']) ?>" class="block">
                            <div class="aspect-[16/10] overflow-hidden bg-maroon/10">
                                <?php if ($item['gambar'] !== ''): ?>
                                    <img src="<?= esc($item['gambar']) ?>" alt="<?= esc($item['judul']) ?>"
                                         class="h-full w-full object-cover transition group-hover:scale-105">
                                <?php else: ?>
                                    <div class="flex h-full items-center justify-center text-maroon/30">
                                        <svg viewBox="0 0 24 24" class="h-12 w-12" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <rect x="3" y="3" width="18" height="18" rx="2"/>
                                            <circle cx="8.5" cy="8.5" r="1.5"/>
                                            <path d="M21 15l-5-5L5 21"/>
                                        </svg>
                                    </div>
                                <?php endif ?>
                            </div>
                            <div class="p-5">
                                <div class="flex flex-wrap items-center gap-2 text-xs">
                                    <span class="rounded-full bg-maroon/10 px-2.5 py-1 font-medium text-maroon">
                                        <?= esc($item['kategoriLabel']) ?>
                                    </span>
                                    <span class="text-stone-500"><?= esc($item['tanggalTerbit']) ?></span>
                                </div>
                                <?php if (($item['tags'] ?? []) !== []): ?>
                                    <div class="mt-2 flex flex-wrap gap-1.5">
                                        <?php foreach ($item['tags'] as $tag): ?>
                                            <span class="rounded-full bg-gold/15 px-2 py-0.5 text-[11px] font-medium text-maroon/80">#<?= esc($tag) ?></span>
                                        <?php endforeach ?>
                                    </div>
                                <?php endif ?>
                                <h2 class="mt-3 font-display text-xl font-semibold text-maroon group-hover:text-maroon/80">
                                    <?= esc($item['judul']) ?>
                                </h2>
                                <?php if ($item['ringkasan'] !== ''): ?>
                                    <p class="mt-2 line-clamp-3 text-sm leading-relaxed text-stone-600">
                                        <?= esc($item['ringkasan']) ?>
                                    </p>
                                <?php endif ?>
                            </div>
                        </a>
                    </article>
                <?php endforeach ?>
            </div>

            <?= view('partials/public_pagination', [
                'pager'              => $pager,
                'queryParams'        => array_filter([
                    'kategori' => $activeKategori ?? '',
                    'tag'      => $activeTag ?? '',
                ]),
                'paginationBaseUrl'  => site_url('berita'),
                'paginationLabel'    => 'berita',
            ]) ?>
        <?php endif ?>
    </div>
</section>
<?= $this->endSection() ?>
