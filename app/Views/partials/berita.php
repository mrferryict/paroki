<section id="berita" class="bg-white py-20 lg:py-28">
    <div class="mx-auto max-w-6xl px-4 sm:px-6">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <span class="inline-block h-1 w-12 bg-gold"></span>
                <h2 class="mt-4 font-display text-3xl font-semibold text-maroon lg:text-4xl">Berita & Kegiatan</h2>
                <p class="mt-3 text-stone-600">Pengumuman dan kegiatan terbaru paroki.</p>
            </div>
            <a href="<?= site_url('berita') ?>"
               class="inline-flex items-center gap-2 text-sm font-semibold text-maroon hover:text-gold transition-colors">
                Lihat semua
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
            </a>
        </div>

        <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <template x-if="beritaList.length === 0">
                <p class="col-span-full rounded-xl border border-gold/20 bg-ivory px-6 py-8 text-center text-sm text-stone-500">
                    Belum ada berita terbit.
                </p>
            </template>
            <template x-for="item in beritaList" :key="item.id">
                <article class="group overflow-hidden rounded-2xl border border-gold/20 bg-ivory shadow-sm transition hover:shadow-md">
                    <a :href="item.href" class="block">
                        <div class="aspect-[16/10] overflow-hidden bg-maroon/10">
                            <template x-if="item.gambar">
                                <img :src="item.gambar" :alt="item.judul" class="h-full w-full object-cover transition group-hover:scale-105">
                            </template>
                            <template x-if="!item.gambar">
                                <div class="flex h-full items-center justify-center text-maroon/30">
                                    <svg viewBox="0 0 24 24" class="h-12 w-12" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                                </div>
                            </template>
                        </div>
                        <div class="p-5">
                            <div class="flex flex-wrap items-center gap-2 text-xs">
                                <span class="rounded-full bg-maroon/10 px-2.5 py-1 font-medium text-maroon" x-text="item.kategoriLabel"></span>
                                <span class="text-stone-500" x-text="item.tanggalTerbit"></span>
                            </div>
                            <h3 class="mt-3 font-display text-xl font-semibold text-maroon group-hover:text-maroon/80" x-text="item.judul"></h3>
                            <p class="mt-2 line-clamp-3 text-sm leading-relaxed text-stone-600" x-text="item.ringkasan"></p>
                        </div>
                    </a>
                </article>
            </template>
        </div>
    </div>
</section>
