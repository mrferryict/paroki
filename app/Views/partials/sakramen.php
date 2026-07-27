<section id="sakramen" class="py-20 lg:py-28">
    <div class="mx-auto max-w-6xl px-4 sm:px-6">
        <div class="mx-auto max-w-2xl text-center">
            <span class="inline-block h-1 w-12 bg-gold"></span>
            <h2 class="mt-4 font-display text-3xl font-semibold text-maroon lg:text-4xl">Sakramen & Layanan</h2>
            <p class="mt-4 text-stone-600">Pendaftaran dan informasi layanan pastoral paroki.</p>
        </div>

        <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <template x-if="sakramenList.length === 0">
                <p class="col-span-full rounded-xl border border-gold/20 bg-white px-6 py-8 text-center text-sm text-stone-500">
                    Data sakramen belum tersedia.
                </p>
            </template>
            <template x-for="item in sakramenList" :key="item.id">
                <article class="group flex flex-col rounded-2xl border border-gold/20 bg-white p-6 shadow-sm transition hover:border-maroon/30 hover:shadow-md">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-maroon/10 text-maroon transition group-hover:bg-maroon group-hover:text-ivory"
                         x-html="iconSvg(item.icon)"></div>
                    <h3 class="mt-4 font-display text-xl font-semibold text-maroon" x-text="item.nama"></h3>
                    <p class="mt-2 flex-1 text-sm leading-relaxed text-stone-600" x-text="item.deskripsi"></p>
                    <button type="button"
                            @click="scrollToForm(item.id)"
                            class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-maroon hover:text-gold transition-colors">
                        Daftar layanan
                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </button>
                </article>
            </template>
        </div>
    </div>
</section>
