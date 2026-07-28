<?php /** @var array<string, mixed> $event */ ?>
<?php
$photos = array_filter($event['items'], static fn (array $item): bool => ($item['jenis'] ?? '') === 'foto');
$videos = array_filter($event['items'], static fn (array $item): bool => ($item['jenis'] ?? '') === 'video');
?>

<?php if ($photos !== []): ?>
    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <?php foreach ($photos as $photo): ?>
            <figure class="overflow-hidden rounded-xl border border-gold/20 bg-white shadow-sm">
                <img src="<?= esc($photo['imageUrl']) ?>" alt="<?= esc($photo['caption']) ?>"
                     class="aspect-[4/3] w-full object-cover" loading="lazy">
                <?php if ($photo['caption'] !== ''): ?>
                    <figcaption class="px-4 py-3 text-sm text-stone-600"><?= esc($photo['caption']) ?></figcaption>
                <?php endif ?>
            </figure>
        <?php endforeach ?>
    </div>
<?php endif ?>

<?php if ($videos !== []): ?>
    <div class="mt-6 grid gap-6 sm:grid-cols-2">
        <?php foreach ($videos as $video): ?>
            <figure class="overflow-hidden rounded-xl border border-gold/20 bg-white shadow-sm">
                <?php if ($video['youtubeEmbedUrl'] !== ''): ?>
                    <div class="aspect-video w-full">
                        <iframe src="<?= esc($video['youtubeEmbedUrl']) ?>"
                                title="<?= esc($video['caption'] ?: 'Video galeri') ?>"
                                class="h-full w-full"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen loading="lazy"></iframe>
                    </div>
                <?php endif ?>
                <?php if ($video['caption'] !== ''): ?>
                    <figcaption class="px-4 py-3 text-sm text-stone-600"><?= esc($video['caption']) ?></figcaption>
                <?php endif ?>
            </figure>
        <?php endforeach ?>
    </div>
<?php endif ?>
