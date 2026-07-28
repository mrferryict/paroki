<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\DTOs\Galeri\GaleriEventDto;
use App\DTOs\Galeri\GaleriItemDto;
use App\Entities\Galeri;
use App\Entities\GaleriEvent;
use App\Enums\GaleriJenis;
use App\Services\GaleriEventService;
use App\Services\GaleriService;
use CodeIgniter\HTTP\ResponseInterface;
use DomainException;
use InvalidArgumentException;
use RuntimeException;

class GaleriController extends BaseController
{
    private GaleriEventService $galeriEventService;

    private GaleriService $galeriService;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger,
    ): void {
        parent::initController($request, $response, $logger);

        $this->galeriEventService = service('galeriEventService');
        $this->galeriService      = service('galeriService');
    }

    public function index(): string
    {
        $rows = $this->galeriService->findAllEventsForAdminTable();

        if ($this->isHtmxRequest()) {
            return view('admin/galeri/partials/table', ['rows' => $rows]);
        }

        return view('admin/galeri/index', [
            'rows'  => $rows,
            'title' => 'Galeri',
        ]);
    }

    public function newEvent(): string
    {
        return view('admin/galeri/partials/event_form', [
            'item'   => null,
            'action' => site_url('admin/galeri/event'),
        ]);
    }

    public function createEvent(): ResponseInterface|string
    {
        if (! $this->validate($this->eventRules(isCreate: true))) {
            return $this->eventValidationErrorResponse();
        }

        try {
            $dto = $this->buildEventDtoFromRequest();
            $this->galeriEventService->create($dto);

            session()->setFlashdata('success', 'Event galeri berhasil ditambahkan.');

            return $this->htmxRedirect(site_url('admin/galeri'));
        } catch (DomainException | RuntimeException $e) {
            return $this->eventFormErrorResponse($e->getMessage());
        }
    }

    public function editEvent(int $id): string
    {
        try {
            $item = $this->galeriEventService->findById($id);
        } catch (DomainException) {
            return $this->eventFormErrorResponse('Event galeri tidak ditemukan.');
        }

        return view('admin/galeri/partials/event_form', [
            'item'   => $item,
            'action' => site_url('admin/galeri/event/' . $id),
        ]);
    }

    public function updateEvent(int $id): ResponseInterface|string
    {
        if (! $this->validate($this->eventRules(isCreate: false))) {
            return $this->eventValidationErrorResponse();
        }

        try {
            $existing = $this->galeriEventService->findById($id);
            $dto      = $this->buildEventDtoFromRequest(
                excludeId: $id,
                urutan: (int) $existing->urutan,
            );

            $this->galeriEventService->update(id: $id, dto: $dto);

            session()->setFlashdata('success', 'Event galeri berhasil diperbarui.');

            return $this->htmxRedirect(site_url('admin/galeri'));
        } catch (DomainException | RuntimeException $e) {
            return $this->eventFormErrorResponse($e->getMessage());
        }
    }

    public function deleteEvent(int $id): ResponseInterface|string
    {
        try {
            $this->galeriEventService->delete($id);

            session()->setFlashdata('success', 'Event galeri berhasil dihapus.');

            return view('admin/galeri/partials/table', [
                'rows' => $this->galeriService->findAllEventsForAdminTable(),
            ]);
        } catch (DomainException | RuntimeException $e) {
            return $this->listErrorResponse($e->getMessage());
        }
    }

    public function moveEventUp(int $id): string
    {
        try {
            $this->galeriEventService->moveUp($id);
        } catch (DomainException | RuntimeException $e) {
            return $this->listErrorResponse($e->getMessage());
        }

        return view('admin/galeri/partials/table', [
            'rows' => $this->galeriService->findAllEventsForAdminTable(),
        ]);
    }

    public function moveEventDown(int $id): string
    {
        try {
            $this->galeriEventService->moveDown($id);
        } catch (DomainException | RuntimeException $e) {
            return $this->listErrorResponse($e->getMessage());
        }

        return view('admin/galeri/partials/table', [
            'rows' => $this->galeriService->findAllEventsForAdminTable(),
        ]);
    }

    public function newItem(int $eventId): string
    {
        try {
            $event = $this->galeriEventService->findById($eventId);
        } catch (DomainException) {
            return $this->itemFormErrorResponse('Event galeri tidak ditemukan.');
        }

        return view('admin/galeri/partials/item_form', [
            'item'         => null,
            'event'        => $event,
            'action'       => site_url('admin/galeri/' . $eventId . '/item'),
            'jenisOptions' => GaleriJenis::options(),
        ]);
    }

    public function createItem(int $eventId): ResponseInterface|string
    {
        if (! $this->validate($this->itemRules(isCreate: true))) {
            return $this->itemValidationErrorResponse(eventId: $eventId);
        }

        try {
            $this->galeriEventService->findById($eventId);
            $dto = $this->buildItemDtoFromRequest(galeriEventId: $eventId);

            $this->galeriService->createItem($dto);

            session()->setFlashdata('success', 'Item galeri berhasil ditambahkan.');

            return $this->htmxRedirect(site_url('admin/galeri'));
        } catch (DomainException | InvalidArgumentException | RuntimeException $e) {
            return $this->itemFormErrorResponse($e->getMessage(), $eventId);
        }
    }

    public function editItem(int $eventId, int $id): string
    {
        try {
            $event = $this->galeriEventService->findById($eventId);
            $item  = $this->galeriService->findItemById($id);

            if ((int) $item->galeri_event_id !== $eventId) {
                throw new DomainException('Item galeri tidak ditemukan.');
            }
        } catch (DomainException) {
            return $this->itemFormErrorResponse('Item galeri tidak ditemukan.', $eventId);
        }

        return view('admin/galeri/partials/item_form', [
            'item'         => $item,
            'event'        => $event,
            'action'       => site_url('admin/galeri/' . $eventId . '/item/' . $id),
            'jenisOptions' => GaleriJenis::options(),
        ]);
    }

    public function updateItem(int $eventId, int $id): ResponseInterface|string
    {
        if (! $this->validate($this->itemRules(isCreate: false))) {
            return $this->itemValidationErrorResponse(eventId: $eventId);
        }

        try {
            $existing = $this->galeriService->findItemById($id);

            if ((int) $existing->galeri_event_id !== $eventId) {
                throw new DomainException('Item galeri tidak ditemukan.');
            }

            $dto = $this->buildItemDtoFromRequest(
                galeriEventId: $eventId,
                existing: $existing,
            );

            $this->galeriService->updateItem(id: $id, dto: $dto);

            session()->setFlashdata('success', 'Item galeri berhasil diperbarui.');

            return $this->htmxRedirect(site_url('admin/galeri'));
        } catch (DomainException | InvalidArgumentException | RuntimeException $e) {
            return $this->itemFormErrorResponse($e->getMessage(), $eventId);
        }
    }

    public function deleteItem(int $eventId, int $id): ResponseInterface|string
    {
        try {
            $existing = $this->galeriService->findItemById($id);

            if ((int) $existing->galeri_event_id !== $eventId) {
                throw new DomainException('Item galeri tidak ditemukan.');
            }

            $this->galeriService->deleteItem($id);

            session()->setFlashdata('success', 'Item galeri berhasil dihapus.');

            return view('admin/galeri/partials/table', [
                'rows' => $this->galeriService->findAllEventsForAdminTable(),
            ]);
        } catch (DomainException | RuntimeException $e) {
            return $this->listErrorResponse($e->getMessage());
        }
    }

    public function moveItemUp(int $eventId, int $id): string
    {
        try {
            $existing = $this->galeriService->findItemById($id);

            if ((int) $existing->galeri_event_id !== $eventId) {
                throw new DomainException('Item galeri tidak ditemukan.');
            }

            $this->galeriService->moveItemUp($id);
        } catch (DomainException | RuntimeException $e) {
            return $this->listErrorResponse($e->getMessage());
        }

        return view('admin/galeri/partials/table', [
            'rows' => $this->galeriService->findAllEventsForAdminTable(),
        ]);
    }

    public function moveItemDown(int $eventId, int $id): string
    {
        try {
            $existing = $this->galeriService->findItemById($id);

            if ((int) $existing->galeri_event_id !== $eventId) {
                throw new DomainException('Item galeri tidak ditemukan.');
            }

            $this->galeriService->moveItemDown($id);
        } catch (DomainException | RuntimeException $e) {
            return $this->listErrorResponse($e->getMessage());
        }

        return view('admin/galeri/partials/table', [
            'rows' => $this->galeriService->findAllEventsForAdminTable(),
        ]);
    }

    /**
     * @return array<string, array<int, string>|string>
     */
    private function eventRules(bool $isCreate): array
    {
        $rules = [
            'judul' => 'required|min_length[2]|max_length[255]',
        ];

        if ($isCreate) {
            $rules['urutan'] = 'permit_empty|is_natural';
        }

        return $rules;
    }

    /**
     * @return array<string, array<int, string>|string>
     */
    private function itemRules(bool $isCreate): array
    {
        $rules = [
            'jenis'       => 'required|in_list[foto,video]',
            'caption'     => 'permit_empty|max_length[2000]',
            'youtube_url' => 'permit_empty|max_length[500]',
        ];

        if ($isCreate) {
            $rules['urutan'] = 'permit_empty|is_natural';
            $rules['file']   = 'if_exist|max_size[file,5120]|mime_in[file,image/jpg,image/jpeg,image/png,image/webp]';
        } else {
            $rules['file'] = 'if_exist|max_size[file,5120]|mime_in[file,image/jpg,image/jpeg,image/png,image/webp]';
        }

        return $rules;
    }

    private function buildEventDtoFromRequest(?int $excludeId = null, int $urutan = 0): GaleriEventDto
    {
        $judul = trim((string) $this->request->getPost('judul'));

        if ($excludeId !== null) {
            return new GaleriEventDto(
                judul: $judul,
                slug: $this->galeriEventService->generateUniqueSlug($judul, $excludeId),
                urutan: $urutan > 0 ? $urutan : (int) ($this->request->getPost('urutan') ?? 0),
            );
        }

        return $this->galeriEventService->buildDto(
            judul: $judul,
            excludeId: null,
            urutan: (int) ($this->request->getPost('urutan') ?? 0),
        );
    }

    private function buildItemDtoFromRequest(int $galeriEventId, ?Galeri $existing = null): GaleriItemDto
    {
        $jenis       = GaleriJenis::from((string) $this->request->getPost('jenis'));
        $caption     = trim((string) $this->request->getPost('caption'));
        $youtubeRaw  = trim((string) $this->request->getPost('youtube_url'));
        $filePath    = $existing !== null ? ((string) ($existing->file_path ?? '') ?: null) : null;
        $youtubeUrl  = $existing !== null ? ((string) ($existing->youtube_url ?? '') ?: null) : null;
        $urutan      = $existing !== null
            ? (int) $existing->urutan
            : (int) ($this->request->getPost('urutan') ?? 0);

        if ($jenis === GaleriJenis::Foto) {
            $uploaded = $this->request->getFile('file');

            if ($uploaded !== null && $uploaded->isValid() && ! $uploaded->hasMoved()) {
                $filePath = $this->galeriService->storeUploadedImage($uploaded);
            }

            $youtubeUrl = null;
        } else {
            $youtubeUrl = $this->galeriService->normalizeYouTubeUrl($youtubeRaw);
            $filePath   = null;
        }

        return new GaleriItemDto(
            galeriEventId: $galeriEventId,
            jenis: $jenis,
            filePath: $filePath,
            youtubeUrl: $youtubeUrl,
            caption: $caption !== '' ? $caption : null,
            urutan: $urutan,
        );
    }

    private function eventValidationErrorResponse(): string
    {
        return view('admin/galeri/partials/event_form', [
            'item'       => $this->eventFromOldInput(),
            'action'     => $this->resolveEventFormAction(),
            'validation' => $this->validator,
        ]);
    }

    private function eventFormErrorResponse(string $message): string
    {
        return view('admin/galeri/partials/event_form_error', ['message' => $message]);
    }

    private function itemValidationErrorResponse(int $eventId): string
    {
        try {
            $event = $this->galeriEventService->findById($eventId);
        } catch (DomainException) {
            return $this->itemFormErrorResponse('Event galeri tidak ditemukan.', $eventId);
        }

        return view('admin/galeri/partials/item_form', [
            'item'         => $this->itemFromOldInput(eventId: $eventId),
            'event'        => $event,
            'action'       => $this->resolveItemFormAction(eventId: $eventId),
            'jenisOptions' => GaleriJenis::options(),
            'validation'   => $this->validator,
        ]);
    }

    private function itemFormErrorResponse(string $message, int $eventId): string
    {
        return view('admin/galeri/partials/item_form_error', [
            'message' => $message,
            'eventId' => $eventId,
        ]);
    }

    private function listErrorResponse(string $message): string
    {
        return view('admin/galeri/partials/list_error', ['message' => $message]);
    }

    private function resolveEventFormAction(): string
    {
        $id = (int) $this->request->getPost('id');

        return $id > 0
            ? site_url('admin/galeri/event/' . $id)
            : site_url('admin/galeri/event');
    }

    private function resolveItemFormAction(int $eventId): string
    {
        $id = (int) $this->request->getPost('id');

        return $id > 0
            ? site_url('admin/galeri/' . $eventId . '/item/' . $id)
            : site_url('admin/galeri/' . $eventId . '/item');
    }

    private function eventFromOldInput(): GaleriEvent
    {
        $item = new GaleriEvent();
        $item->id     = (int) ($this->request->getPost('id') ?? 0);
        $item->judul  = (string) $this->request->getPost('judul');
        $item->urutan = (int) ($this->request->getPost('urutan') ?? 0);

        return $item;
    }

    private function itemFromOldInput(int $eventId): Galeri
    {
        $item = new Galeri();
        $item->id              = (int) ($this->request->getPost('id') ?? 0);
        $item->galeri_event_id = $eventId;
        $item->jenis           = (string) $this->request->getPost('jenis');
        $item->caption         = trim((string) $this->request->getPost('caption')) ?: null;
        $item->youtube_url     = trim((string) $this->request->getPost('youtube_url')) ?: null;
        $item->file_path       = (string) ($this->request->getPost('file_existing') ?? '');
        $item->urutan          = (int) ($this->request->getPost('urutan') ?? 0);

        return $item;
    }
}
