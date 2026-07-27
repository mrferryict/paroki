<section id="jadwal" class="bg-maroon py-20 text-ivory lg:py-28">
    <div class="relative mx-auto max-w-6xl px-4 sm:px-6">
        <div class="absolute inset-0 opacity-[0.06]" style="background-image: radial-gradient(circle at 1px 1px, #C5A572 1px, transparent 0); background-size: 26px 26px;"></div>
        <div class="relative">
            <div class="mx-auto max-w-2xl text-center">
                <span class="inline-block h-1 w-12 bg-gold"></span>
                <h2 class="mt-4 font-display text-3xl font-semibold lg:text-4xl">Jadwal Misa</h2>
                <p class="mt-4 text-ivory/75">Mari bergabung dalam perayaan Ekaristi. Berikut jadwal misa di paroki.</p>
            </div>

            <?php if ($jadwalList === []): ?>
                <p class="relative mt-10 rounded-2xl bg-white/5 px-6 py-8 text-center text-sm text-ivory/70 ring-1 ring-white/10">
                    Jadwal misa belum tersedia.
                </p>
            <?php else: ?>
                <div class="relative mt-12 grid gap-5 sm:grid-cols-2">
                    <?php foreach ($jadwalList as $jadwal): ?>
                        <article class="rounded-2xl bg-white/10 p-6 ring-1 ring-white/10 backdrop-blur-sm transition hover:bg-white/15">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-sm font-medium uppercase tracking-wide text-gold">
                                        <?= esc($jadwal['jenisLabel']) ?>
                                    </p>
                                    <p class="mt-1 font-display text-2xl font-semibold">
                                        <?= esc($jadwal['hariLabel']) ?>
                                    </p>
                                </div>
                                <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-gold/20 text-gold">
                                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                                </span>
                            </div>
                            <p class="mt-5 font-display text-2xl font-bold"><?= esc($jadwal['jam']) ?></p>
                            <?php if ($jadwal['catatan'] !== ''): ?>
                                <p class="mt-3 text-sm text-ivory/70"><?= esc($jadwal['catatan']) ?></p>
                            <?php endif ?>
                        </article>
                    <?php endforeach ?>
                </div>
            <?php endif ?>

            <div class="relative mt-8 rounded-2xl bg-gold/10 px-6 py-5 ring-1 ring-gold/30">
                <p class="text-center text-sm text-ivory/90 sm:text-left">
                    <span class="font-semibold text-gold">Intensi Misa</span> dapat disampaikan melalui formulir pendaftaran di bawah.
                </p>
            </div>
        </div>
    </div>
</section>
