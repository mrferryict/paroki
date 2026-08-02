<button type="button"
        x-show="showScrollTop"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
        @click="scrollToTop()"
        class="fixed z-40 flex h-12 w-12 items-center justify-center rounded-full border border-gold/40 bg-maroon text-ivory shadow-lg shadow-maroon/30 transition hover:bg-maroon/90 active:scale-95 sm:h-11 sm:w-11"
        style="bottom: max(1.25rem, env(safe-area-inset-bottom)); right: max(1rem, env(safe-area-inset-right));"
        aria-label="Kembali ke atas">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/>
    </svg>
</button>
