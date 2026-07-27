<section id="katekese" class="py-20 lg:py-28">
    <div class="mx-auto max-w-6xl px-4 sm:px-6">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <span class="inline-block h-1 w-12 bg-gold"></span>
                <h2 class="mt-4 font-display text-3xl font-semibold text-maroon lg:text-4xl">Katekese & Renungan</h2>
                <p class="mt-3 text-stone-600">Artikel iman, renungan harian, orang kudus, dan mutiara biblika.</p>
            </div>
            <a href="<?= site_url('katekese') ?>"
               class="inline-flex items-center gap-2 text-sm font-semibold text-maroon hover:text-gold transition-colors">
                Arsip lengkap
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
            </a>
        </div>

        <div class="mt-8 flex flex-wrap gap-2">
            <template x-for="tab in katekeseKategori" :key="tab.value">
                <button type="button"
                        @click="activeKatekeseTab = tab.value"
                        class="rounded-full px-4 py-2 text-sm font-medium transition"
                        :class="activeKatekeseTab === tab.value ? 'bg-maroon text-ivory' : 'bg-white text-stone-600 ring-1 ring-gold/30 hover:bg-maroon/5'"
                        x-text="tab.label"></button>
            </template>
        </div>

        <div class="mt-10 grid gap-5 sm:grid-cols-2">
            <template x-if="filteredKatekese.length === 0">
                <p class="col-span-full rounded-xl border border-gold/20 bg-white px-6 py-8 text-center text-sm text-stone-500">
                    Belum ada artikel untuk kategori ini.
                </p>
            </template>
            <template x-for="item in filteredKatekese" :key="item.id">
                <article class="rounded-2xl border border-gold/20 bg-white p-6 shadow-sm transition hover:shadow-md">
                    <div class="flex items-center gap-2 text-xs">
                        <span class="rounded-full bg-gold/20 px-2.5 py-1 font-medium text-maroon" x-text="item.kategoriLabel"></span>
                        <span class="text-stone-500" x-text="item.tanggalTerbit"></span>
                    </div>
                    <h3 class="mt-3 font-display text-xl font-semibold text-maroon">
                        <a :href="item.href" class="hover:text-maroon/80" x-text="item.judul"></a>
                    </h3>
                    <p class="mt-2 text-sm leading-relaxed text-stone-600" x-text="item.ringkasan"></p>
                    <a :href="item.href"
                       class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-maroon hover:text-gold transition-colors">
                        Baca selengkapnya
                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </a>
                </article>
            </template>
        </div>
    </div>
</section>
