<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\DTOs\Dokumen\DokumenDto;
use App\Entities\Dokumen;
use App\Services\DokumenService;
use CodeIgniter\HTTP\ResponseInterface;
use DomainException;
use InvalidArgumentException;
use RuntimeException;

class DokumenController extends BaseController
{
    private DokumenService $dokumenService;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger,
    ): void {
        parent::initController($request, $response, $logger);

        $this->dokumenService = service('dokumenService');
    }

    public function index(): string
    {
        $items = $this->dokumenService->findAllOrdered();

        if ($this->isHtmxRequest()) {
            return view('admin/dokumen/partials/list', ['items' => $items]);
        }

        return view('admin/dokumen/index', [
            'items' => $items,
            'title' => 'Dokumen',
        ]);
    }

    public function new(): string
    {
        return view('admin/dokumen/partials/form', [
            'item'   => null,
            'action' => site_url('admin/dokumen'),
        ]);
    }

    public function create(): ResponseInterface|string
    {
        if (! $this->validate($this->createRules())) {
            return $this->validationErrorResponse();
        }

        try {
            $filePath = $this->dokumenService->storeUploadedFile($this->request->getFile('file'));
            $dto      = $this->buildDtoFromRequest(filePath: $filePath);

            $this->dokumenService->create($dto);

            session()->setFlashdata('success', 'Dokumen berhasil ditambahkan.');

            return $this->htmxRedirect(site_url('admin/dokumen'));
        } catch (InvalidArgumentException | RuntimeException $e) {
            return $this->formErrorResponse($e->getMessage());
        }
    }

    public function edit(int $id): string
    {
        try {
            $item = $this->dokumenService->findById($id);
        } catch (DomainException) {
            return $this->formErrorResponse('Dokumen tidak ditemukan.');
        }

        return view('admin/dokumen/partials/form', [
            'item'   => $item,
            'action' => site_url('admin/dokumen/' . $id),
        ]);
    }

    public function update(int $id): ResponseInterface|string
    {
        if (! $this->validate($this->updateRules())) {
            return $this->validationErrorResponse();
        }

        try {
            $existing = $this->dokumenService->findById($id);
            $filePath = (string) $existing->file_path;

            $uploaded = $this->request->getFile('file');

            if ($uploaded !== null && $uploaded->isValid() && ! $uploaded->hasMoved()) {
                $filePath = $this->dokumenService->storeUploadedFile($uploaded);
            }

            $dto = $this->buildDtoFromRequest(filePath: $filePath);

            $this->dokumenService->update(id: $id, dto: $dto);

            session()->setFlashdata('success', 'Dokumen berhasil diperbarui.');

            return $this->htmxRedirect(site_url('admin/dokumen'));
        } catch (DomainException | InvalidArgumentException | RuntimeException $e) {
            return $this->formErrorResponse($e->getMessage());
        }
    }

    public function delete(int $id): ResponseInterface|string
    {
        try {
            $this->dokumenService->delete($id);

            session()->setFlashdata('success', 'Dokumen berhasil dihapus.');

            return view('admin/dokumen/partials/list', [
                'items' => $this->dokumenService->findAllOrdered(),
            ]);
        } catch (DomainException | RuntimeException $e) {
            return $this->listErrorResponse($e->getMessage());
        }
    }

    /**
     * @return array<string, array<int, string>|string>
     */
    private function createRules(): array
    {
        return [
            'nama'     => 'required|min_length[2]|max_length[255]',
            'kategori' => 'required|min_length[2]|max_length[100]',
            'file'     => 'uploaded[file]|max_size[file,10240]|ext_in[file,pdf,doc,docx,xls,xlsx,jpg,jpeg,png]',
        ];
    }

    /**
     * @return array<string, array<int, string>|string>
     */
    private function updateRules(): array
    {
        return [
            'nama'     => 'required|min_length[2]|max_length[255]',
            'kategori' => 'required|min_length[2]|max_length[100]',
            'file'     => 'if_exist|max_size[file,10240]|ext_in[file,pdf,doc,docx,xls,xlsx,jpg,jpeg,png]',
        ];
    }

    private function buildDtoFromRequest(string $filePath): DokumenDto
    {
        return new DokumenDto(
            nama: trim((string) $this->request->getPost('nama')),
            filePath: $filePath,
            kategori: trim((string) $this->request->getPost('kategori')),
        );
    }

    private function validationErrorResponse(): string
    {
        return view('admin/dokumen/partials/form', [
            'item'       => $this->requestFromOldInput(),
            'action'     => $this->resolveFormAction(),
            'validation' => $this->validator,
        ]);
    }

    private function formErrorResponse(string $message): string
    {
        return view('admin/dokumen/partials/form_error', ['message' => $message]);
    }

    private function listErrorResponse(string $message): string
    {
        return view('admin/dokumen/partials/list_error', ['message' => $message]);
    }

    private function resolveFormAction(): string
    {
        $id = (int) $this->request->getPost('id');

        return $id > 0
            ? site_url('admin/dokumen/' . $id)
            : site_url('admin/dokumen');
    }

    private function requestFromOldInput(): Dokumen
    {
        $item = new Dokumen();
        $item->id       = (int) ($this->request->getPost('id') ?? 0);
        $item->nama     = (string) $this->request->getPost('nama');
        $item->kategori = (string) $this->request->getPost('kategori');

        return $item;
    }
}
