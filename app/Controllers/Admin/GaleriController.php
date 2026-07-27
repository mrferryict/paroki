<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\DTOs\Galeri\GaleriDto;
use App\Entities\Galeri;
use App\Services\GaleriService;
use CodeIgniter\HTTP\ResponseInterface;
use DomainException;
use InvalidArgumentException;
use RuntimeException;

class GaleriController extends BaseController
{
    private GaleriService $galeriService;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger,
    ): void {
        parent::initController($request, $response, $logger);

        $this->galeriService = service('galeriService');
    }

    public function index(): string
    {
        $items = $this->galeriService->findAllOrdered();

        if ($this->isHtmxRequest()) {
            return view('admin/galeri/partials/list', ['items' => $items]);
        }

        return view('admin/galeri/index', [
            'items' => $items,
            'title' => 'Galeri',
        ]);
    }

    public function new(): string
    {
        return view('admin/galeri/partials/form', [
            'item'   => null,
            'action' => site_url('admin/galeri'),
        ]);
    }

    public function create(): ResponseInterface|string
    {
        if (! $this->validate($this->createRules())) {
            return $this->validationErrorResponse();
        }

        try {
            $filePath = $this->galeriService->storeUploadedImage($this->request->getFile('file'));
            $dto      = $this->buildDtoFromRequest(filePath: $filePath);

            $this->galeriService->create($dto);

            session()->setFlashdata('success', 'Foto galeri berhasil ditambahkan.');

            return $this->htmxRedirect(site_url('admin/galeri'));
        } catch (InvalidArgumentException | RuntimeException $e) {
            return $this->formErrorResponse($e->getMessage());
        }
    }

    public function edit(int $id): string
    {
        try {
            $item = $this->galeriService->findById($id);
        } catch (DomainException) {
            return $this->formErrorResponse('Item galeri tidak ditemukan.');
        }

        return view('admin/galeri/partials/form', [
            'item'   => $item,
            'action' => site_url('admin/galeri/' . $id),
        ]);
    }

    public function update(int $id): ResponseInterface|string
    {
        if (! $this->validate($this->updateRules())) {
            return $this->validationErrorResponse();
        }

        try {
            $existing = $this->galeriService->findById($id);
            $filePath = (string) $existing->file_path;

            $uploaded = $this->request->getFile('file');

            if ($uploaded !== null && $uploaded->isValid() && ! $uploaded->hasMoved()) {
                $filePath = $this->galeriService->storeUploadedImage($uploaded);
            }

            $dto = $this->buildDtoFromRequest(
                filePath: $filePath,
                urutan: (int) $existing->urutan,
            );

            $this->galeriService->update(id: $id, dto: $dto);

            session()->setFlashdata('success', 'Foto galeri berhasil diperbarui.');

            return $this->htmxRedirect(site_url('admin/galeri'));
        } catch (DomainException | InvalidArgumentException | RuntimeException $e) {
            return $this->formErrorResponse($e->getMessage());
        }
    }

    public function delete(int $id): ResponseInterface|string
    {
        try {
            $this->galeriService->delete($id);

            session()->setFlashdata('success', 'Foto galeri berhasil dihapus.');

            return view('admin/galeri/partials/list', [
                'items' => $this->galeriService->findAllOrdered(),
            ]);
        } catch (DomainException | RuntimeException $e) {
            return $this->listErrorResponse($e->getMessage());
        }
    }

    public function moveUp(int $id): string
    {
        try {
            $this->galeriService->moveUp($id);
        } catch (DomainException | RuntimeException $e) {
            return $this->listErrorResponse($e->getMessage());
        }

        return view('admin/galeri/partials/list', [
            'items' => $this->galeriService->findAllOrdered(),
        ]);
    }

    public function moveDown(int $id): string
    {
        try {
            $this->galeriService->moveDown($id);
        } catch (DomainException | RuntimeException $e) {
            return $this->listErrorResponse($e->getMessage());
        }

        return view('admin/galeri/partials/list', [
            'items' => $this->galeriService->findAllOrdered(),
        ]);
    }

    /**
     * @return array<string, array<int, string>|string>
     */
    private function createRules(): array
    {
        return [
            'file'    => 'uploaded[file]|max_size[file,5120]|mime_in[file,image/jpg,image/jpeg,image/png,image/webp]',
            'caption' => 'permit_empty|max_length[2000]',
            'urutan'  => 'permit_empty|is_natural',
        ];
    }

    /**
     * @return array<string, array<int, string>|string>
     */
    private function updateRules(): array
    {
        return [
            'file'    => 'if_exist|max_size[file,5120]|mime_in[file,image/jpg,image/jpeg,image/png,image/webp]',
            'caption' => 'permit_empty|max_length[2000]',
        ];
    }

    private function buildDtoFromRequest(string $filePath, int $urutan = 0): GaleriDto
    {
        $caption = trim((string) $this->request->getPost('caption'));

        return new GaleriDto(
            filePath: $filePath,
            caption: $caption !== '' ? $caption : null,
            urutan: $urutan > 0 ? $urutan : (int) ($this->request->getPost('urutan') ?? 0),
        );
    }

    private function validationErrorResponse(): string
    {
        return view('admin/galeri/partials/form', [
            'item'       => $this->requestFromOldInput(),
            'action'     => $this->resolveFormAction(),
            'validation' => $this->validator,
        ]);
    }

    private function formErrorResponse(string $message): string
    {
        return view('admin/galeri/partials/form_error', ['message' => $message]);
    }

    private function listErrorResponse(string $message): string
    {
        return view('admin/galeri/partials/list_error', ['message' => $message]);
    }

    private function resolveFormAction(): string
    {
        $id = (int) $this->request->getPost('id');

        return $id > 0
            ? site_url('admin/galeri/' . $id)
            : site_url('admin/galeri');
    }

    private function requestFromOldInput(): Galeri
    {
        $item = new Galeri();
        $item->id        = (int) ($this->request->getPost('id') ?? 0);
        $item->caption   = trim((string) $this->request->getPost('caption')) ?: null;
        $item->file_path = (string) ($this->request->getPost('file_existing') ?? '');
        $item->urutan    = (int) ($this->request->getPost('urutan') ?? 0);

        return $item;
    }
}
