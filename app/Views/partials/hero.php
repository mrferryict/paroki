<?php
$heroSlides = $heroSlides ?? [];
$siteName   = $title ?? 'Paroki Santo Mikael Gombong';
?>
<section id="hero" class="relative min-h-[100dvh] overflow-hidden pt-16">
    <?php if ($heroSlides === []): ?>
        <div class="flex min-h-[calc(100dvh-4rem)] items-center justify-center bg-maroon px-4">
            <div class="max-w-xl text-center text-ivory">
                <p class="text-sm uppercase tracking-[0.2em] text-gold">Selamat Datang</p>
                <h1 class="mt-4 font-display text-4xl font-semibold sm:text-5xl"><?= esc($siteName) ?></h1>
                <p class="mt-4 text-ivory/80">Persekutuan umat beriman yang bertumbuh dalam doa, pelayanan, dan kebersamaan.</p>
            </div>
        </div>
    <?php else: ?>
        <div class="relative min-h-[calc(100dvh-4rem)]">
            <template x-for="(slide, index) in heroSlides" :key="index">
                <div x-show="currentSlide === index"
                     x-transition:enter="hero-slide-enter"
                     class="absolute inset-0">
                    <img :src="slide.gambar"
                         :alt="slide.judul"
                         class="h-full w-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-maroon/90 via-maroon/50 to-maroon/30"></div>
                </div>
            </template>

            <div class="relative z-10 mx-auto flex min-h-[calc(100dvh-4rem)] max-w-6xl flex-col justify-end px-4 pb-16 pt-10 sm:px-6 sm:pb-20 lg:justify-center">
                <template x-if="currentHero">
                    <div class="max-w-2xl text-ivory">
                        <p x-show="currentHero.eyebrow"
                           x-text="currentHero.eyebrow"
                           class="mb-4 inline-flex items-center gap-2 text-sm font-medium uppercase tracking-[0.2em] text-gold"></p>
                        <h1 class="whitespace-pre-line font-display text-4xl font-semibold leading-tight sm:text-5xl lg:text-6xl"
                            x-text="currentHero.judul"></h1>
                        <p x-show="currentHero.subjudul"
                           x-text="currentHero.subjudul"
                           class="mt-5 max-w-xl text-lg text-ivory/90 sm:text-xl"></p>
                        <div class="mt-8 flex flex-wrap gap-3">
                            <a x-show="currentHero.cta1Label"
                               :href="currentHero.cta1Href"
                               x-text="currentHero.cta1Label"
                               class="inline-flex items-center justify-center rounded-lg bg-gold px-6 py-3 text-sm font-semibold text-maroon shadow-sm transition hover:bg-gold/90"></a>
                            <a x-show="currentHero.cta2Label"
                               :href="currentHero.cta2Href"
                               x-text="currentHero.cta2Label"
                               class="inline-flex items-center justify-center rounded-lg border border-ivory/40 bg-ivory/10 px-6 py-3 text-sm font-semibold text-ivory backdrop-blur-sm transition hover:bg-ivory/20"></a>
                        </div>
                    </div>
                </template>
            </div>

            <template x-if="heroSlides.length > 1">
                <div>
                    <button type="button"
                            @click="prevSlide(); startSlideTimer()"
                            class="absolute left-4 top-1/2 z-20 -translate-y-1/2 rounded-full bg-maroon/60 p-2 text-ivory backdrop-blur hover:bg-maroon/80"
                            aria-label="Slide sebelumnya">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button type="button"
                            @click="nextSlide(); startSlideTimer()"
                            class="absolute right-4 top-1/2 z-20 -translate-y-1/2 rounded-full bg-maroon/60 p-2 text-ivory backdrop-blur hover:bg-maroon/80"
                            aria-label="Slide berikutnya">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                    <div class="absolute bottom-8 left-1/2 z-20 flex -translate-x-1/2 gap-2">
                        <template x-for="(slide, index) in heroSlides" :key="'dot-' + index">
                            <button type="button"
                                    @click="goToSlide(index)"
                                    class="h-2.5 rounded-full transition-all"
                                    :class="currentSlide === index ? 'w-8 bg-gold' : 'w-2.5 bg-ivory/50'"
                                    :aria-label="'Slide ' + (index + 1)"></button>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    <?php endif ?>
</section>
