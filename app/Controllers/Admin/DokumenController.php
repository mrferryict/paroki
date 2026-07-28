<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\DTOs\Dokumen\DokumenDto;
use App\Entities\Dokumen;
use App\Services\DokumenKategoriService;
use App\Services\DokumenService;
use CodeIgniter\HTTP\ResponseInterface;
use DomainException;
use InvalidArgumentException;
use RuntimeException;

class DokumenController extends BaseController
{
    private DokumenService $dokumenService;

    private DokumenKategoriService $dokumenKategoriService;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger,
    ): void {
        parent::initController($request, $response, $logger);

        $this->dokumenService         = service('dokumenService');
        $this->dokumenKategoriService = service('dokumenKategoriService');
    }

    public function index(): string
    {
        $items = $this->dokumenService->findAllOrdered();

        if ($this->isHtmxRequest()) {
            return view('admin/dokumen/partials/list', [
                'items'           => $items,
                'kategoriOptions' => $this->dokumenService->allKategoriLabels(),
            ]);
        }

        return view('admin/dokumen/index', [
            'items'           => $items,
            'kategoriOptions' => $this->dokumenService->allKategoriLabels(),
            'title'           => 'Unduhan',
        ]);
    }

    public function new(): string
    {
        return view('admin/dokumen/partials/form', [
            'item'            => null,
            'action'          => site_url('admin/dokumen'),
            'kategoriOptions' => $this->dokumenService->kategoriOptions(),
        ]);
    }

    public function create(): ResponseInterface|string
    {
        if (! $this->validate($this->createRules())) {
            return $this->validationErrorResponse(null);
        }

        try {
            $filePath = $this->dokumenService->storeUploadedFile($this->request->getFile('file'));
            $dto      = $this->buildDtoFromRequest(filePath: $filePath);

            $this->dokumenService->create($dto);

            session()->setFlashdata('success', 'Unduhan berhasil ditambahkan.');

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
            return $this->formErrorResponse('Unduhan tidak ditemukan.');
        }

        return view('admin/dokumen/partials/form', [
            'item'            => $item,
            'action'          => site_url('admin/dokumen/' . $id),
            'kategoriOptions' => $this->dokumenService->kategoriOptionsForAdmin((string) ($item->kategori ?? '')),
        ]);
    }

    public function update(int $id): ResponseInterface|string
    {
        if (! $this->validate($this->updateRules(id: $id))) {
            return $this->validationErrorResponse($id);
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

            session()->setFlashdata('success', 'Unduhan berhasil diperbarui.');

            return $this->htmxRedirect(site_url('admin/dokumen'));
        } catch (DomainException | InvalidArgumentException | RuntimeException $e) {
            return $this->formErrorResponse($e->getMessage());
        }
    }

    public function delete(int $id): ResponseInterface|string
    {
        try {
            $this->dokumenService->delete($id);

            session()->setFlashdata('success', 'Unduhan berhasil dihapus.');

            return view('admin/dokumen/partials/list', [
                'items'           => $this->dokumenService->findAllOrdered(),
                'kategoriOptions' => $this->dokumenService->allKategoriLabels(),
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
            'kategori' => $this->kategoriInListRule(activeOnly: true),
            'file'     => 'uploaded[file]|max_size[file,10240]|ext_in[file,pdf,doc,docx,xls,xlsx,jpg,jpeg,png]',
        ];
    }

    /**
     * @return array<string, array<int, string>|string>
     */
    private function updateRules(int $id): array
    {
        try {
            $existing = $this->dokumenService->findById($id);
            $current  = (string) ($existing->kategori ?? '');
        } catch (DomainException) {
            $current = null;
        }

        return [
            'nama'     => 'required|min_length[2]|max_length[255]',
            'kategori' => $this->kategoriInListRule(activeOnly: false, currentSlug: $current),
            'file'     => 'if_exist|max_size[file,10240]|ext_in[file,pdf,doc,docx,xls,xlsx,jpg,jpeg,png]',
        ];
    }

    private function kategoriInListRule(bool $activeOnly, ?string $currentSlug = null): string
    {
        $slugs = $activeOnly
            ? $this->dokumenKategoriService->activeSlugList()
            : array_keys($this->dokumenService->kategoriOptionsForAdmin($currentSlug));

        if ($slugs === []) {
            return 'required';
        }

        return 'required|in_list[' . implode(',', $slugs) . ']';
    }

    private function buildDtoFromRequest(string $filePath): DokumenDto
    {
        return new DokumenDto(
            nama: trim((string) $this->request->getPost('nama')),
            filePath: $filePath,
            kategori: trim((string) $this->request->getPost('kategori')),
        );
    }

    private function validationErrorResponse(?int $id): string
    {
        $item = $this->requestFromOldInput();
        $slug = (string) ($item->kategori ?? '');

        return view('admin/dokumen/partials/form', [
            'item'            => $item,
            'action'          => $this->resolveFormAction(),
            'kategoriOptions' => $id !== null && $id > 0
                ? $this->dokumenService->kategoriOptionsForAdmin($slug)
                : $this->dokumenService->kategoriOptions(),
            'validation'      => $this->validator,
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
