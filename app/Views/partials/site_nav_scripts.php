<script>
    function siteNavBase() {
        return {
            navOpen: false,
            shareUrl: <?= json_encode($shareUrl ?? current_url(), JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
            shareTitle: <?= json_encode($shareTitle ?? 'Paroki Santo Mikael Gombong', JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
            shareNoticeTimer: null,
            showScrollTop: false,

            init() {
                this.initScrollTop();
            },

            initScrollTop() {
                const update = () => {
                    this.showScrollTop = window.scrollY > 320;
                };

                update();
                window.addEventListener('scroll', update, { passive: true });
            },

            scrollToTop() {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },

            showShareNotice(message) {
                let toast = document.getElementById('share-notice-toast');

                if (! toast) {
                    toast = document.createElement('div');
                    toast.id = 'share-notice-toast';
                    toast.setAttribute('role', 'status');
                    toast.setAttribute('aria-live', 'polite');
                    toast.hidden = true;
                    toast.className = 'pointer-events-none fixed bottom-4 left-1/2 z-[60] max-w-sm -translate-x-1/2 rounded-lg border border-gold/30 bg-maroon px-4 py-3 text-center text-sm font-medium text-ivory shadow-lg';
                    document.body.appendChild(toast);
                }

                toast.textContent = message;
                toast.hidden = false;

                clearTimeout(this.shareNoticeTimer);
                this.shareNoticeTimer = setTimeout(() => {
                    toast.hidden = true;
                    toast.textContent = '';
                }, 4000);
            },

            canUseNativeShare(payload) {
                if (! navigator.share) {
                    return false;
                }

                if (typeof navigator.canShare === 'function' && ! navigator.canShare(payload)) {
                    return false;
                }

                // Native share sheet is reliable on phones/tablets; desktop often has no useful UI.
                return window.matchMedia('(pointer: coarse)').matches;
            },

            async copyShareUrl() {
                const url = this.shareUrl;

                if (navigator.clipboard?.writeText && window.isSecureContext) {
                    await navigator.clipboard.writeText(url);

                    return true;
                }

                const textarea = document.createElement('textarea');
                textarea.value = url;
                textarea.setAttribute('readonly', '');
                textarea.style.position = 'fixed';
                textarea.style.left = '-9999px';
                document.body.appendChild(textarea);
                textarea.select();
                const copied = document.execCommand('copy');
                document.body.removeChild(textarea);

                return copied;
            },

            async sharePage() {
                const payload = {
                    title: this.shareTitle,
                    text: this.shareTitle,
                    url: this.shareUrl,
                };

                if (this.canUseNativeShare(payload)) {
                    try {
                        await navigator.share(payload);

                        return;
                    } catch (error) {
                        if (error?.name === 'AbortError') {
                            return;
                        }
                    }
                }

                try {
                    if (await this.copyShareUrl()) {
                        this.showShareNotice('Tautan disalin. Tempel di chat atau media sosial.');

                        return;
                    }
                } catch {
                    // Fall through to manual copy hint.
                }

                this.showShareNotice('Salin tautan: ' + this.shareUrl);
            },
        };
    }
</script>
