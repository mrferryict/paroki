<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Galeri\GaleriEventAdminRowDto;
use App\DTOs\Galeri\GaleriItemAdminRowDto;
use App\DTOs\Galeri\GaleriItemDto;
use App\Entities\Galeri;
use App\Enums\GaleriJenis;
use App\Libraries\ImageResizer;
use App\Libraries\PublicUploadDirectory;
use App\Models\GaleriEventModel;
use App\Models\GaleriModel;
use CodeIgniter\HTTP\Files\UploadedFile;
use DomainException;
use InvalidArgumentException;
use RuntimeException;
use Uri\InvalidUriException;
use Uri\Rfc3986\Uri;

class GaleriService
{
    private const UPLOAD_SUBDIR = 'uploads/galeri';

    /** @var list<string> */
    private const ALLOWED_IMAGE_MIMES = [
        'image/jpeg',
        'image/png',
        'image/webp',
    ];

    public function __construct(
        private readonly GaleriModel $galeriModel,
        private readonly GaleriEventModel $galeriEventModel,
        private readonly ImageResizer $imageResizer,
    ) {}

    /**
     * @return list<GaleriEventAdminRowDto>
     */
    public function findAllEventsForAdminTable(): array
    {
        $events = $this->galeriEventModel
            ->orderBy('urutan', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();

        if ($events === []) {
            return [];
        }

        $eventIds = array_map(static fn ($event): int => (int) $event->id, $events);

        /** @var list<Galeri> $items */
        $items = $this->galeriModel
            ->whereIn('galeri_event_id', $eventIds)
            ->orderBy('urutan', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();

        /** @var array<int, list<GaleriItemAdminRowDto>> $itemsByEvent */
        $itemsByEvent = [];

        foreach ($items as $item) {
            $eventId = (int) $item->galeri_event_id;
            $jenis   = GaleriJenis::tryFrom((string) ($item->jenis ?? '')) ?? GaleriJenis::Foto;

            $itemsByEvent[$eventId][] = new GaleriItemAdminRowDto(
                id: (int) $item->id,
                galeriEventId: $eventId,
                jenis: $jenis->value,
                jenisLabel: $jenis->label(),
                filePath: $item->file_path !== null && $item->file_path !== '' ? (string) $item->file_path : null,
                youtubeUrl: $item->youtube_url !== null && $item->youtube_url !== '' ? (string) $item->youtube_url : null,
                caption: $item->caption !== null && $item->caption !== '' ? (string) $item->caption : null,
                urutan: (int) ($item->urutan ?? 0),
                previewUrl: $this->resolvePreviewUrl($item),
            );
        }

        $rows = [];

        foreach ($events as $event) {
            $eventId = (int) $event->id;

            $rows[] = new GaleriEventAdminRowDto(
                id: $eventId,
                judul: (string) ($event->judul ?? ''),
                slug: (string) ($event->slug ?? ''),
                urutan: (int) ($event->urutan ?? 0),
                viewCount: (int) ($event->view_count ?? 0),
                items: $itemsByEvent[$eventId] ?? [],
            );
        }

        return $rows;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function findPublishedForPublic(): array
    {
        $events = $this->galeriEventModel
            ->orderBy('urutan', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();

        if ($events === []) {
            return [];
        }

        return $this->mapEventsForPublic($events);
    }

    /**
     * @param list<\App\Entities\GaleriEvent> $events
     *
     * @return list<array<string, mixed>>
     */
    private function mapEventsForPublic(array $events): array
    {
        if ($events === []) {
            return [];
        }

        $eventIds = array_map(static fn ($event): int => (int) $event->id, $events);

        /** @var list<Galeri> $items */
        $items = $this->galeriModel
            ->whereIn('galeri_event_id', $eventIds)
            ->orderBy('urutan', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();

        /** @var array<int, list<array<string, mixed>>> $itemsByEvent */
        $itemsByEvent = [];

        foreach ($items as $item) {
            $eventId = (int) $item->galeri_event_id;
            $jenis   = (string) ($item->jenis ?? GaleriJenis::Foto->value);

            $mapped = [
                'id'      => (int) $item->id,
                'jenis'   => $jenis,
                'caption' => (string) ($item->caption ?? ''),
            ];

            if ($jenis === GaleriJenis::Video->value) {
                $mapped['youtubeEmbedUrl'] = $this->youtubeEmbedUrl((string) ($item->youtube_url ?? ''));
            } else {
                $mapped['imageUrl'] = $this->publicUrl((string) ($item->file_path ?? ''));
            }

            $itemsByEvent[$eventId][] = $mapped;
        }

        $result = [];

        foreach ($events as $event) {
            $eventId    = (int) $event->id;
            $eventItems = $itemsByEvent[$eventId] ?? [];

            if ($eventItems === []) {
                continue;
            }

            $result[] = [
                'id'    => $eventId,
                'judul' => (string) ($event->judul ?? ''),
                'slug'  => (string) ($event->slug ?? ''),
                'items' => $eventItems,
            ];
        }

        return $result;
    }

    /**
     * @return array<string, mixed>
     */
    public function findPublishedEventBySlug(string $slug): array
    {
        $event = $this->galeriEventModel->where('slug', $slug)->first();

        if ($event === null) {
            throw new DomainException('Event galeri tidak ditemukan.');
        }

        $events = $this->mapEventsForPublic([$event]);
        $first  = $events[0] ?? null;

        if ($first === null) {
            throw new DomainException('Event galeri tidak ditemukan.');
        }

        return $first;
    }

    public function incrementEventViewCount(int $eventId): void
    {
        $this->galeriEventModel
            ->where('id', $eventId)
            ->set('view_count', 'view_count + 1', false)
            ->update();
    }

    public function findItemById(int $id): Galeri
    {
        $item = $this->galeriModel->find($id);

        if ($item === null) {
            throw new DomainException('Item galeri tidak ditemukan.');
        }

        return $item;
    }

    public function createItem(GaleriItemDto $dto): int
    {
        $this->validateItemDto($dto);

        $data           = $dto->toModelData();
        $data['urutan'] = $this->resolveItemUrutan(galeriEventId: $dto->galeriEventId, requestedUrutan: $dto->urutan);

        $id = $this->galeriModel->insert($data);

        if ($id === false) {
            throw new RuntimeException('Gagal menyimpan item galeri.');
        }

        return (int) $id;
    }

    public function updateItem(int $id, GaleriItemDto $dto): void
    {
        $existing = $this->findItemById($id);
        $this->validateItemDto($dto);

        $data           = $dto->toModelData();
        $data['urutan'] = $dto->urutan > 0 ? $dto->urutan : (int) $existing->urutan;

        if (! $this->galeriModel->update($id, $data)) {
            throw new RuntimeException('Gagal memperbarui item galeri.');
        }

        $oldPath = (string) ($existing->file_path ?? '');

        if ($oldPath !== '' && $oldPath !== ($dto->filePath ?? '')) {
            $this->removeFileIfUnused($oldPath);
        }
    }

    public function deleteItem(int $id): void
    {
        $existing = $this->findItemById($id);

        if (! $this->galeriModel->delete($id)) {
            throw new RuntimeException('Gagal menghapus item galeri.');
        }

        $this->removeFileIfUnused((string) ($existing->file_path ?? ''));
    }

    public function moveItemUp(int $id): void
    {
        $current = $this->findItemById($id);

        $neighbor = $this->galeriModel
            ->where('galeri_event_id', (int) $current->galeri_event_id)
            ->where('urutan <', (int) $current->urutan)
            ->orderBy('urutan', 'DESC')
            ->first();

        if ($neighbor === null) {
            return;
        }

        $this->swapItemUrutan($current, $neighbor);
    }

    public function moveItemDown(int $id): void
    {
        $current = $this->findItemById($id);

        $neighbor = $this->galeriModel
            ->where('galeri_event_id', (int) $current->galeri_event_id)
            ->where('urutan >', (int) $current->urutan)
            ->orderBy('urutan', 'ASC')
            ->first();

        if ($neighbor === null) {
            return;
        }

        $this->swapItemUrutan($current, $neighbor);
    }

    public function storeUploadedImage(UploadedFile $file): string
    {
        if (! $file->isValid()) {
            throw new InvalidArgumentException($file->getErrorString() ?: 'File gambar tidak valid.');
        }

        $mime = $file->getMimeType();

        if ($mime === null || ! in_array($mime, self::ALLOWED_IMAGE_MIMES, true)) {
            throw new InvalidArgumentException('Format gambar harus JPEG, PNG, atau WebP.');
        }

        $targetDir = PublicUploadDirectory::ensure(self::UPLOAD_SUBDIR);

        $storedName = $file->getRandomName();

        if (! $file->move($targetDir, $storedName)) {
            throw new RuntimeException('Gagal mengunggah gambar galeri.');
        }

        $fullPath = $targetDir . '/' . $storedName;
        $this->imageResizer->resizeToMaxBox(fullPath: $fullPath, maxWidth: 1200, maxHeight: 900);

        return self::UPLOAD_SUBDIR . '/' . $storedName;
    }

    public function normalizeYouTubeUrl(string $url): string
    {
        $url = trim($url);

        if ($url === '') {
            throw new InvalidArgumentException('URL YouTube wajib diisi untuk item video.');
        }

        $videoId = $this->extractYouTubeVideoId($url);

        if ($videoId === null) {
            throw new InvalidArgumentException('URL YouTube tidak valid. Gunakan link youtube.com atau youtu.be.');
        }

        return 'https://www.youtube.com/watch?v=' . $videoId;
    }

    public function youtubeEmbedUrl(string $url): string
    {
        $videoId = $this->extractYouTubeVideoId($url);

        if ($videoId === null) {
            return '';
        }

        return 'https://www.youtube.com/embed/' . $videoId;
    }

    public function publicUrl(string $relativePath): string
    {
        if ($relativePath === '') {
            return '';
        }

        return base_url(ltrim($relativePath, '/'));
    }

    public function resolveItemUrutan(int $galeriEventId, int $requestedUrutan): int
    {
        if ($requestedUrutan > 0) {
            return $requestedUrutan;
        }

        $row = $this->galeriModel
            ->selectMax('urutan')
            ->where('galeri_event_id', $galeriEventId)
            ->first();

        if ($row === null) {
            return 1;
        }

        return ((int) ($row->urutan ?? 0)) + 1;
    }

    private function validateItemDto(GaleriItemDto $dto): void
    {
        if ($dto->jenis === GaleriJenis::Foto) {
            if ($dto->filePath === null || $dto->filePath === '') {
                throw new InvalidArgumentException('Foto wajib diunggah untuk item jenis foto.');
            }

            return;
        }

        $this->normalizeYouTubeUrl((string) $dto->youtubeUrl);
    }

    private function extractYouTubeVideoId(string $url): ?string
    {
        $url = trim($url);

        if ($url === '') {
            return null;
        }

        try {
            $uri = new Uri($url);
        } catch (InvalidUriException) {
            return null;
        }

        $host = strtolower($uri->getHost() ?? '');

        if ($host === 'youtu.be') {
            $videoId = trim($uri->getPath(), '/');

            return $this->isValidYouTubeVideoId($videoId) ? $videoId : null;
        }

        if (! str_ends_with($host, 'youtube.com') && ! str_ends_with($host, 'youtube-nocookie.com')) {
            return null;
        }

        $path = $uri->getPath() ?? '';

        if (preg_match('#^/embed/([A-Za-z0-9_-]{11})$#', $path, $matches) === 1) {
            return $matches[1];
        }

        if ($path === '/watch' || $path === '/watch/') {
            parse_str($uri->getQuery() ?? '', $query);
            $videoId = (string) ($query['v'] ?? '');

            return $this->isValidYouTubeVideoId($videoId) ? $videoId : null;
        }

        return null;
    }

    private function isValidYouTubeVideoId(string $videoId): bool
    {
        return preg_match('/^[A-Za-z0-9_-]{11}$/', $videoId) === 1;
    }

    private function resolvePreviewUrl(Galeri $item): ?string
    {
        $jenis = (string) ($item->jenis ?? GaleriJenis::Foto->value);

        if ($jenis === GaleriJenis::Video->value) {
            $videoId = $this->extractYouTubeVideoId((string) ($item->youtube_url ?? ''));

            return $videoId !== null
                ? 'https://img.youtube.com/vi/' . $videoId . '/hqdefault.jpg'
                : null;
        }

        $filePath = (string) ($item->file_path ?? '');

        return $filePath !== '' ? $this->publicUrl($filePath) : null;
    }

    private function swapItemUrutan(Galeri $a, Galeri $b): void
    {
        $db = db_connect();
        $db->transStart();

        $this->galeriModel->update((int) $a->id, ['urutan' => (int) $b->urutan]);
        $this->galeriModel->update((int) $b->id, ['urutan' => (int) $a->urutan]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new RuntimeException('Gagal mengubah urutan item galeri.');
        }
    }

    private function removeFileIfUnused(string $relativePath): void
    {
        if ($relativePath === '') {
            return;
        }

        if ($this->galeriModel->where('file_path', $relativePath)->countAllResults() > 0) {
            return;
        }

        $fullPath = FCPATH . ltrim($relativePath, '/');

        if (is_file($fullPath)) {
            unlink($fullPath);
        }
    }
}
