<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\DTOs\Artikel\ArtikelKategoriDto;
use App\Entities\ArtikelKategoriRecord;
use App\Services\ArtikelKategoriService;
use CodeIgniter\HTTP\ResponseInterface;
use DomainException;
use RuntimeException;

class KatekeseKategoriController extends BaseController
{
    private ArtikelKategoriService $artikelKategoriService;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger,
    ): void {
        parent::initController($request, $response, $logger);

        $this->artikelKategoriService = service('artikelKategoriService');
    }

    public function index(): string
    {
        $items = $this->artikelKategoriService->findAllOrdered();

        if ($this->isHtmxRequest()) {
            return view('admin/katekese_kategori/partials/list', ['items' => $items]);
        }

        return view('admin/katekese_kategori/index', [
            'items' => $items,
            'title' => 'Kategori Katekese',
        ]);
    }

    public function new(): string
    {
        return view('admin/katekese_kategori/partials/form', [
            'item'   => null,
            'action' => site_url('admin/katekese-kategori'),
        ]);
    }

    public function create(): ResponseInterface|string
    {
        if (! $this->validate($this->formRules())) {
            return $this->validationErrorResponse();
        }

        try {
            $this->artikelKategoriService->create($this->buildDtoFromRequest());

            session()->setFlashdata('success', 'Kategori katekese berhasil ditambahkan.');

            return $this->htmxRedirect(site_url('admin/katekese-kategori'));
        } catch (DomainException | RuntimeException $e) {
            return $this->formErrorResponse($e->getMessage());
        }
    }

    public function edit(int $id): string
    {
        try {
            $item = $this->artikelKategoriService->findById($id);
        } catch (DomainException) {
            return $this->formErrorResponse('Kategori katekese tidak ditemukan.');
        }

        return view('admin/katekese_kategori/partials/form', [
            'item'   => $item,
            'action' => site_url('admin/katekese-kategori/' . $id),
        ]);
    }

    public function update(int $id): ResponseInterface|string
    {
        if (! $this->validate($this->formRules())) {
            return $this->validationErrorResponse();
        }

        try {
            $existing = $this->artikelKategoriService->findById($id);
            $this->artikelKategoriService->update(
                id: $id,
                dto: $this->buildDtoFromRequest(existing: $existing, excludeId: $id),
            );

            session()->setFlashdata('success', 'Kategori katekese berhasil diperbarui.');

            return $this->htmxRedirect(site_url('admin/katekese-kategori'));
        } catch (DomainException | RuntimeException $e) {
            return $this->formErrorResponse($e->getMessage());
        }
    }

    public function delete(int $id): ResponseInterface|string
    {
        try {
            $this->artikelKategoriService->delete($id);

            session()->setFlashdata('success', 'Kategori katekese berhasil dihapus.');

            return view('admin/katekese_kategori/partials/list', [
                'items' => $this->artikelKategoriService->findAllOrdered(),
            ]);
        } catch (DomainException | RuntimeException $e) {
            return $this->listErrorResponse($e->getMessage());
        }
    }

    /**
     * @return array<string, array<int, string>|string>
     */
    private function formRules(): array
    {
        return [
            'label'     => 'required|min_length[2]|max_length[255]',
            'urutan'    => 'permit_empty|is_natural',
            'is_active' => 'required|in_list[0,1]',
        ];
    }

    private function buildDtoFromRequest(?ArtikelKategoriRecord $existing = null, ?int $excludeId = null): ArtikelKategoriDto
    {
        $label    = trim((string) $this->request->getPost('label'));
        $isActive = (string) $this->request->getPost('is_active') === '1';

        if ($existing !== null) {
            $existingSlug = (string) ($existing->slug ?? '');
            $slug         = $this->artikelKategoriService->hasArtikelUsingSlug($existingSlug)
                ? $existingSlug
                : $this->artikelKategoriService->generateUniqueSlug($label, $excludeId);

            return new ArtikelKategoriDto(
                slug: $slug,
                label: $label,
                urutan: (int) ($existing->urutan ?? 0),
                isActive: $isActive,
            );
        }

        return new ArtikelKategoriDto(
            slug: $this->artikelKategoriService->generateUniqueSlug($label, $excludeId),
            label: $label,
            urutan: $this->artikelKategoriService->resolveUrutan((int) ($this->request->getPost('urutan') ?? 0)),
            isActive: $isActive,
        );
    }

    private function validationErrorResponse(): string
    {
        return view('admin/katekese_kategori/partials/form', [
            'item'       => $this->requestFromOldInput(),
            'action'     => $this->resolveFormAction(),
            'validation' => $this->validator,
        ]);
    }

    private function formErrorResponse(string $message): string
    {
        return view('admin/katekese_kategori/partials/form_error', ['message' => $message]);
    }

    private function listErrorResponse(string $message): string
    {
        return view('admin/katekese_kategori/partials/list_error', ['message' => $message]);
    }

    private function resolveFormAction(): string
    {
        $id = (int) $this->request->getPost('id');

        return $id > 0
            ? site_url('admin/katekese-kategori/' . $id)
            : site_url('admin/katekese-kategori');
    }

    private function requestFromOldInput(): ArtikelKategoriRecord
    {
        $item = new ArtikelKategoriRecord();
        $item->id        = (int) ($this->request->getPost('id') ?? 0);
        $item->label     = (string) $this->request->getPost('label');
        $item->urutan    = (int) ($this->request->getPost('urutan') ?? 0);
        $item->is_active = (string) $this->request->getPost('is_active') === '1';

        return $item;
    }
}
