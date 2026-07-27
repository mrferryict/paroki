<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\DTOs\Artikel\ArtikelDto;
use App\DTOs\Shared\ContentListFilterDto;
use App\Entities\Artikel;
use App\Enums\ArtikelKategori;
use App\Enums\PublishStatus;
use App\Services\ArtikelService;
use CodeIgniter\HTTP\ResponseInterface;
use DomainException;
use InvalidArgumentException;
use RuntimeException;

class ArtikelController extends BaseController
{
    private ArtikelService $artikelService;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger,
    ): void {
        parent::initController($request, $response, $logger);

        $this->artikelService = service('artikelService');
    }

    public function index(?string $kategori = null): string
    {
        $resolvedKategori = $this->resolveRouteKategori($kategori);
        $filter           = $this->buildFilterFromRequest(defaultKategori: $resolvedKategori);
        $result           = $this->artikelService->findPaginated($filter);

        $viewData = [
            'result'          => $result,
            'filter'          => $filter,
            'activeKategori'  => $resolvedKategori,
            'kategoriOptions' => $this->artikelService->kategoriOptions(),
            'statusOptions'   => $this->artikelService->statusOptions(),
        ];

        if ($this->isHtmxRequest()) {
            return view('admin/artikel/partials/list', $viewData);
        }

        return view('admin/artikel/index', array_merge($viewData, ['title' => 'Katekese & Renungan']));
    }

    public function new(?string $kategori = null): string
    {
        $resolvedKategori = $this->resolveRouteKategori($kategori);

        return view('admin/artikel/partials/form', [
            'item'            => null,
            'action'          => site_url('admin/artikel'),
            'defaultKategori' => $resolvedKategori,
            'kategoriOptions' => $this->artikelService->kategoriOptions(),
            'statusOptions'   => $this->artikelService->statusOptions(),
        ]);
    }

    public function create(): ResponseInterface|string
    {
        if (! $this->validate($this->formRules())) {
            return $this->validationErrorResponse();
        }

        try {
            $dto = $this->buildDtoFromRequest();

            $this->artikelService->create($dto);

            session()->setFlashdata('success', 'Artikel berhasil ditambahkan.');

            $redirect = $dto->kategori->value !== ''
                ? site_url('admin/artikel/kategori/' . $dto->kategori->value)
                : site_url('admin/artikel');

            return $this->htmxRedirect($redirect);
        } catch (InvalidArgumentException | RuntimeException $e) {
            return $this->formErrorResponse($e->getMessage());
        }
    }

    public function edit(int $id): string
    {
        try {
            $item = $this->artikelService->findById($id);
        } catch (DomainException) {
            return $this->formErrorResponse('Artikel tidak ditemukan.');
        }

        return view('admin/artikel/partials/form', [
            'item'            => $item,
            'action'          => site_url('admin/artikel/' . $id),
            'defaultKategori' => null,
            'kategoriOptions' => $this->artikelService->kategoriOptions(),
            'statusOptions'   => $this->artikelService->statusOptions(),
        ]);
    }

    public function update(int $id): ResponseInterface|string
    {
        if (! $this->validate($this->formRules())) {
            return $this->validationErrorResponse();
        }

        try {
            $dto = $this->buildDtoFromRequest(excludeId: $id);

            $this->artikelService->update(id: $id, dto: $dto);

            session()->setFlashdata('success', 'Artikel berhasil diperbarui.');

            return $this->htmxRedirect(site_url('admin/artikel/kategori/' . $dto->kategori->value));
        } catch (DomainException | InvalidArgumentException | RuntimeException $e) {
            return $this->formErrorResponse($e->getMessage());
        }
    }

    public function delete(int $id): ResponseInterface|string
    {
        try {
            $existing = $this->artikelService->findById($id);
            $this->artikelService->delete($id);

            session()->setFlashdata('success', 'Artikel berhasil dihapus.');

            $kategoriValue = $existing->kategori instanceof ArtikelKategori
                ? $existing->kategori->value
                : (string) $existing->kategori;

            $filter = new ContentListFilterDto(
                kategori: $kategoriValue,
                status: $this->nullableGet('status'),
                page: max(1, (int) ($this->request->getGet('page') ?? 1)),
            );

            return view('admin/artikel/partials/list', [
                'result'          => $this->artikelService->findPaginated($filter),
                'filter'          => $filter,
                'activeKategori'  => $kategoriValue,
                'kategoriOptions' => $this->artikelService->kategoriOptions(),
                'statusOptions'   => $this->artikelService->statusOptions(),
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
            'judul'          => 'required|min_length[3]|max_length[255]',
            'kategori'       => 'required|in_list[artikel_iman,renungan_harian,orang_kudus,mutiara_biblika]',
            'konten'         => 'permit_empty',
            'status'         => 'required|in_list[draft,terbit]',
            'tanggal_terbit' => 'permit_empty|valid_date[Y-m-d\TH:i]',
        ];
    }

    private function buildFilterFromRequest(?string $defaultKategori = null): ContentListFilterDto
    {
        return new ContentListFilterDto(
            kategori: $this->nullableGet('kategori') ?? $defaultKategori,
            status: $this->nullableGet('status'),
            page: max(1, (int) ($this->request->getGet('page') ?? 1)),
            perPage: 10,
        );
    }

    private function buildDtoFromRequest(?int $excludeId = null): ArtikelDto
    {
        $judul    = trim((string) $this->request->getPost('judul'));
        $status   = PublishStatus::from((string) $this->request->getPost('status'));
        $kategori = ArtikelKategori::from((string) $this->request->getPost('kategori'));
        $konten   = trim((string) $this->request->getPost('konten'));

        return $this->artikelService->buildAdminDto(
            judul: $judul,
            kategori: $kategori,
            konten: $konten !== '' ? $konten : null,
            status: $status,
            tanggalTerbitRaw: $this->nullablePost('tanggal_terbit'),
            excludeId: $excludeId,
        );
    }

    private function resolveRouteKategori(?string $kategori): ?string
    {
        if ($kategori === null || $kategori === '') {
            return null;
        }

        $enum = ArtikelKategori::tryFromString($kategori);

        if ($enum === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return $enum->value;
    }

    private function nullableGet(string $field): ?string
    {
        $value = trim((string) $this->request->getGet($field));

        return $value !== '' ? $value : null;
    }

    private function nullablePost(string $field): ?string
    {
        $value = trim((string) $this->request->getPost($field));

        return $value !== '' ? $value : null;
    }

    private function validationErrorResponse(): string
    {
        return view('admin/artikel/partials/form', [
            'item'            => $this->requestFromOldInput(),
            'action'          => $this->resolveFormAction(),
            'defaultKategori' => $this->nullablePost('kategori'),
            'kategoriOptions' => $this->artikelService->kategoriOptions(),
            'statusOptions'   => $this->artikelService->statusOptions(),
            'validation'      => $this->validator,
        ]);
    }

    private function formErrorResponse(string $message): string
    {
        return view('admin/artikel/partials/form_error', ['message' => $message]);
    }

    private function listErrorResponse(string $message): string
    {
        return view('admin/artikel/partials/list_error', ['message' => $message]);
    }

    private function resolveFormAction(): string
    {
        $id = (int) $this->request->getPost('id');

        return $id > 0
            ? site_url('admin/artikel/' . $id)
            : site_url('admin/artikel');
    }

    private function requestFromOldInput(): Artikel
    {
        $item = new Artikel();
        $item->id       = (int) ($this->request->getPost('id') ?? 0);
        $item->judul    = (string) $this->request->getPost('judul');
        $item->kategori = (string) $this->request->getPost('kategori');
        $item->konten   = $this->nullablePost('konten');
        $item->status   = (string) $this->request->getPost('status');

        return $item;
    }
}
