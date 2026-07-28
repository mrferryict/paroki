<?php
/** @var bool $isHome */
/** @var string $shareUrl */
/** @var string $shareTitle */
$isHome     = $isHome ?? false;
$shareUrl   = $shareUrl ?? current_url();
$shareTitle = $shareTitle ?? 'Paroki Hati Kudus Yesus';
$paroki     = config('Paroki');
$branding   = service('siteSettingService')->getBranding();
$logoUrl    = $branding['logoUrl'] ?? null;
$siteName   = $branding['siteName'] ?? $shareTitle;
$homeUrl    = site_url('/');
$section    = static fn (string $id): string => $isHome ? '#' . $id : $homeUrl . '#' . $id;
?>
<header class="fixed inset-x-0 top-0 z-50 border-b border-gold/20 bg-maroon/95 text-ivory backdrop-blur-md">
    <div class="mx-auto flex max-w-6xl items-center justify-between gap-3 px-4 py-3">
        <a href="<?= $isHome ? '#hero' : esc($homeUrl) ?>"
           class="flex min-w-0 items-center gap-3 hover:text-gold transition-colors">
            <?php if ($logoUrl !== null): ?>
                <img src="<?= esc($logoUrl) ?>" alt="" class="h-10 w-10 shrink-0 rounded-full border border-gold/30 bg-white object-contain p-0.5">
            <?php endif ?>
            <span class="truncate font-display text-lg font-semibold tracking-tight sm:text-2xl"><?= esc($siteName) ?></span>
        </a>
        <nav class="hidden items-center gap-5 text-sm font-medium lg:flex">
            <a href="<?= esc($section('profil')) ?>" class="hover:text-gold transition-colors">Profil</a>
            <a href="<?= esc($section('jadwal')) ?>" class="hover:text-gold transition-colors">Jadwal</a>
            <a href="<?= esc($section('layanan')) ?>" class="hover:text-gold transition-colors">Layanan</a>
            <a href="<?= site_url('berita') ?>" class="hover:text-gold transition-colors">Berita</a>
            <a href="<?= site_url('katekese') ?>" class="hover:text-gold transition-colors">Katekese</a>
            <a href="<?= site_url('galeri') ?>" class="hover:text-gold transition-colors">Galeri</a>
            <a href="<?= site_url('unduhan') ?>" class="hover:text-gold transition-colors">Unduhan</a>
        </nav>
        <div class="flex items-center gap-2">
            <a href="<?= esc($paroki->whatsappUrl()) ?>"
               target="_blank"
               rel="noopener noreferrer"
               class="hidden items-center gap-1.5 rounded-lg bg-green-600 px-3 py-2 text-xs font-semibold text-white hover:bg-green-700 transition-colors sm:inline-flex">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.625.846 5.059 2.284 7.034L.789 23.492a.5.5 0 0 0 .611.611l4.458-1.495A11.953 11.953 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-2.34 0-4.516-.715-6.32-1.938l-.452-.271-3.053 1.025 1.025-3.053-.271-.452A9.956 9.956 0 0 1 2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
                WhatsApp
            </a>
            <button type="button"
                    @click="sharePage()"
                    class="hidden items-center gap-1.5 rounded-lg border border-gold/30 px-3 py-2 text-xs font-semibold text-ivory hover:bg-gold/10 transition-colors sm:inline-flex">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 12v7a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-7M16 6l-4-4-4 4M12 2v14"/></svg>
                Bagikan
            </button>
            <button type="button"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-gold/10 lg:hidden"
                    @click="navOpen = !navOpen"
                    :aria-expanded="navOpen"
                    aria-label="Menu">
                <svg x-show="!navOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg x-show="navOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>
    <div x-show="navOpen" x-transition class="border-t border-gold/20 px-4 py-4 lg:hidden">
        <div class="flex flex-col gap-3 text-sm font-medium">
            <a href="<?= esc($section('profil')) ?>" @click="navOpen=false">Profil</a>
            <a href="<?= esc($section('jadwal')) ?>" @click="navOpen=false">Jadwal</a>
            <a href="<?= esc($section('layanan')) ?>" @click="navOpen=false">Layanan</a>
            <a href="<?= site_url('berita') ?>" @click="navOpen=false">Berita</a>
            <a href="<?= site_url('katekese') ?>" @click="navOpen=false">Katekese</a>
            <a href="<?= site_url('galeri') ?>" @click="navOpen=false">Galeri</a>
            <a href="<?= site_url('unduhan') ?>" @click="navOpen=false">Unduhan</a>
            <div class="flex flex-wrap gap-2 border-t border-gold/20 pt-3">
                <a href="<?= esc($paroki->whatsappUrl()) ?>" target="_blank" rel="noopener noreferrer"
                   class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white">
                    WhatsApp
                </a>
                <button type="button" @click="sharePage(); navOpen=false"
                        class="rounded-lg border border-gold/30 px-4 py-2 text-sm font-semibold">
                    Bagikan halaman
                </button>
            </div>
        </div>
    </div>
</header>
