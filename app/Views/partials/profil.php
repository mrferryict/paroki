<section id="profil" class="py-20 lg:py-28">
    <div class="mx-auto max-w-6xl px-4 sm:px-6">
        <div class="grid items-center gap-12 lg:grid-cols-2 lg:gap-16">
            <div class="relative">
                <div class="aspect-[4/5] overflow-hidden rounded-2xl bg-maroon/10 shadow-xl shadow-maroon/10">
                    <div class="flex h-full items-center justify-center bg-gradient-to-br from-maroon/20 to-gold/20 p-8 text-center">
                        <div>
                            <svg viewBox="0 0 24 24" class="mx-auto h-16 w-16 text-maroon/40" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 3v18M8 7h8M6 21h12l-2-10H8l-2 10z"/></svg>
                            <p class="mt-4 font-display text-2xl font-semibold text-maroon">Gereja Paroki</p>
                        </div>
                    </div>
                </div>
                <div class="absolute -bottom-4 -right-2 rounded-xl bg-maroon px-5 py-4 text-ivory shadow-lg sm:right-6">
                    <p class="font-display text-2xl font-bold">DPH</p>
                    <p class="text-xs uppercase tracking-wide text-gold">Dewan Paroki Hati</p>
                </div>
            </div>
            <div>
                <span class="inline-block h-1 w-12 bg-gold"></span>
                <h2 class="mt-4 font-display text-3xl font-semibold text-maroon lg:text-4xl">Profil Paroki Santo Mikael Gombong</h2>
                <div class="mt-5 space-y-4 text-[17px] leading-relaxed text-stone-600">
                    <p>Paroki Santo Mikael Gombong adalah persekutuan umat beriman yang bertumbuh dalam doa, pelayanan, dan kebersamaan. Kami mengundang setiap orang untuk merasakan rumah rohani yang hangat dan terbuka.</p>
                    <p>Melalui Dewan Paroki Hati (DPH) dan struktur wilayah–lingkungan, umat dilibatkan aktif dalam liturgi, diakonia, koinonia, dan kerygma.</p>
                </div>
            </div>
        </div>

        <!-- Dewan Paroki Hati -->
        <div class="mt-20">
            <div class="text-center">
                <span class="inline-block h-1 w-12 bg-gold"></span>
                <h3 class="mt-4 font-display text-3xl font-semibold text-maroon">Dewan Paroki Hati (DPH)</h3>
                <p class="mt-3 text-stone-600">Empat bidang pelayanan paroki</p>
            </div>
            <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <template x-for="bidang in bidangDPH" :key="bidang.kode">
                    <article class="rounded-2xl border border-gold/20 bg-white p-6 shadow-sm transition hover:shadow-md">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-maroon/10 text-maroon"
                             x-html="iconSvg(bidang.icon)"></div>
                        <h4 class="mt-4 font-display text-xl font-semibold text-maroon" x-text="bidang.nama"></h4>
                        <p class="mt-2 text-sm leading-relaxed text-stone-600" x-text="bidang.deskripsi"></p>
                        <template x-if="bidang.penjabat && bidang.penjabat.length > 0">
                            <ul class="mt-4 space-y-1 border-t border-gold/10 pt-3 text-sm text-stone-600">
                                <template x-for="pj in bidang.penjabat" :key="pj.id">
                                    <li x-text="pj.nama"></li>
                                </template>
                            </ul>
                        </template>
                    </article>
                </template>
            </div>
        </div>

        <!-- Wilayah & Lingkungan -->
        <div class="mt-20">
            <div class="text-center">
                <span class="inline-block h-1 w-12 bg-gold"></span>
                <h3 class="mt-4 font-display text-3xl font-semibold text-maroon">Wilayah & Lingkungan</h3>
                <p class="mt-3 text-stone-600">Struktur komunitas umat paroki</p>
            </div>
            <div class="mt-10 space-y-3">
                <template x-if="wilayahList.length === 0">
                    <p class="rounded-xl border border-gold/20 bg-white px-6 py-8 text-center text-sm text-stone-500">Data wilayah belum tersedia.</p>
                </template>
                <template x-for="wilayah in wilayahList" :key="wilayah.id">
                    <div class="overflow-hidden rounded-xl border border-gold/20 bg-white shadow-sm">
                        <button type="button"
                                @click="toggleWilayah(wilayah.id)"
                                class="flex w-full items-center justify-between px-5 py-4 text-left transition hover:bg-maroon/5">
                            <div>
                                <p class="font-display text-lg font-semibold text-maroon" x-text="wilayah.nama"></p>
                                <p class="mt-0.5 text-sm text-stone-500">
                                    Koordinator: <span x-text="wilayah.ketuaNama"></span>
                                    · <span x-text="wilayah.lingkungan.length"></span> lingkungan
                                </p>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="h-5 w-5 shrink-0 text-maroon transition-transform"
                                 :class="activeWilayah === wilayah.id ? 'rotate-180' : ''"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="activeWilayah === wilayah.id" x-collapse>
                            <ul class="divide-y divide-stone-100 border-t border-gold/10 px-5 py-2">
                                <template x-for="ling in wilayah.lingkungan" :key="ling.id">
                                    <li class="flex items-center justify-between py-3 text-sm">
                                        <span class="font-medium text-stone-800" x-text="ling.nama"></span>
                                        <span class="text-stone-500">Ketua: <span x-text="ling.ketuaNama"></span></span>
                                    </li>
                                </template>
                                <template x-if="wilayah.lingkungan.length === 0">
                                    <li class="py-3 text-sm text-stone-500">Belum ada lingkungan terdaftar.</li>
                                </template>
                            </ul>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</section>
