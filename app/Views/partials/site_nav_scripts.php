<script>
    function siteNavBase() {
        return {
            navOpen: false,
            shareUrl: <?= json_encode($shareUrl ?? current_url(), JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
            shareTitle: <?= json_encode($shareTitle ?? 'Paroki Santo Mikael Gombong', JSON_HEX_APOS | JSON_HEX_QUOT) ?>,

            async sharePage() {
                const payload = { title: this.shareTitle, url: this.shareUrl };

                if (navigator.share) {
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
                    await navigator.clipboard.writeText(this.shareUrl);
                    alert('Tautan halaman disalin ke clipboard.');
                } catch {
                    prompt('Salin tautan halaman ini:', this.shareUrl);
                }
            },
        };
    }
</script>
