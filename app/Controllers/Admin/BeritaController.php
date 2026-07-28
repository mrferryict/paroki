<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\DTOs\Berita\BeritaDto;
use App\DTOs\Shared\ContentListFilterDto;
use App\Entities\Berita;
use App\Enums\BeritaKategori;
use App\Enums\PublishStatus;
use App\Services\BeritaService;
use CodeIgniter\HTTP\ResponseInterface;
use DomainException;
use InvalidArgumentException;
use RuntimeException;

class BeritaController extends BaseController
{
    private BeritaService $beritaService;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger,
    ): void {
        parent::initController($request, $response, $logger);

        $this->beritaService = service('beritaService');
    }

    public function index(): string
    {
        $filter = $this->buildFilterFromRequest();
        $result = $this->beritaService->findPaginated($filter);

        $viewData = [
            'result'          => $result,
            'filter'          => $filter,
            'kategoriOptions' => $this->beritaService->kategoriOptions(),
            'statusOptions'   => $this->beritaService->statusOptions(),
        ];

        if ($this->isHtmxRequest()) {
            return view('admin/berita/partials/list', $viewData);
        }

        return view('admin/berita/index', array_merge($viewData, ['title' => 'Berita & Kegiatan']));
    }

    public function new(): string
    {
        return view('admin/berita/partials/form', [
            'item'            => null,
            'action'          => site_url('admin/berita'),
            'kategoriOptions' => $this->beritaService->kategoriOptions(),
            'statusOptions'   => $this->beritaService->statusOptions(),
        ]);
    }

    public function create(): ResponseInterface|string
    {
        if (! $this->validate($this->createRules())) {
            return $this->validationErrorResponse();
        }

        try {
            $gambar = $this->beritaService->resolveUploadedImage(
                $this->request->getFile('gambar_utama'),
                required: true,
            );
            $dto = $this->buildDtoFromRequest(gambarUtama: $gambar);

            $this->beritaService->create($dto);

            session()->setFlashdata('success', 'Berita berhasil ditambahkan.');

            return $this->htmxRedirect(site_url('admin/berita'));
        } catch (InvalidArgumentException | RuntimeException $e) {
            return $this->formErrorResponse($e->getMessage());
        }
    }

    public function edit(int $id): string
    {
        try {
            $item = $this->beritaService->findById($id);
        } catch (DomainException) {
            return $this->formErrorResponse('Berita tidak ditemukan.');
        }

        return view('admin/berita/partials/form', [
            'item'            => $item,
            'action'          => site_url('admin/berita/' . $id),
            'kategoriOptions' => $this->beritaService->kategoriOptions(),
            'statusOptions'   => $this->beritaService->statusOptions(),
        ]);
    }

    public function update(int $id): ResponseInterface|string
    {
        if (! $this->validate($this->updateRules())) {
            return $this->validationErrorResponse();
        }

        try {
            $existing = $this->beritaService->findById($id);
            $gambar   = $this->beritaService->resolveUploadedImageForUpdate(
                $this->request->getFile('gambar_utama'),
                (string) ($existing->gambar_utama ?? ''),
            );

            $dto = $this->buildDtoFromRequest(
                gambarUtama: $gambar !== '' ? $gambar : null,
                excludeId: $id,
            );

            $this->beritaService->update(id: $id, dto: $dto);

            session()->setFlashdata('success', 'Berita berhasil diperbarui.');

            return $this->htmxRedirect(site_url('admin/berita'));
        } catch (DomainException | InvalidArgumentException | RuntimeException $e) {
            return $this->formErrorResponse($e->getMessage());
        }
    }

    public function delete(int $id): ResponseInterface|string
    {
        try {
            $this->beritaService->delete($id);

            session()->setFlashdata('success', 'Berita berhasil dihapus.');

            return view('admin/berita/partials/list', [
                'result'          => $this->beritaService->findPaginated($this->buildFilterFromRequest()),
                'filter'          => $this->buildFilterFromRequest(),
                'kategoriOptions' => $this->beritaService->kategoriOptions(),
                'statusOptions'   => $this->beritaService->statusOptions(),
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
            'judul'          => 'required|min_length[3]|max_length[255]',
            'kategori'       => 'required|in_list[pengumuman,kegiatan_paroki,pelayanan_sosial,kegiatan_wilayah,liturgi]',
            'ringkasan'      => 'permit_empty|max_length[2000]',
            'tags'           => 'permit_empty|max_length[500]',
            'konten'         => 'permit_empty',
            'status'         => 'required|in_list[draft,terbit]',
            'tanggal_terbit' => 'permit_empty|valid_date[Y-m-d\TH:i]',
            'gambar_utama'   => 'uploaded[gambar_utama]|max_size[gambar_utama,2048]|ext_in[gambar_utama,jpg,jpeg,png,webp]',
        ];
    }

    /**
     * @return array<string, array<int, string>|string>
     */
    private function updateRules(): array
    {
        return [
            'judul'          => 'required|min_length[3]|max_length[255]',
            'kategori'       => 'required|in_list[pengumuman,kegiatan_paroki,pelayanan_sosial,kegiatan_wilayah,liturgi]',
            'ringkasan'      => 'permit_empty|max_length[2000]',
            'tags'           => 'permit_empty|max_length[500]',
            'konten'         => 'permit_empty',
            'status'         => 'required|in_list[draft,terbit]',
            'tanggal_terbit' => 'permit_empty|valid_date[Y-m-d\TH:i]',
            'gambar_utama'   => 'if_exist|max_size[gambar_utama,2048]|ext_in[gambar_utama,jpg,jpeg,png,webp]',
        ];
    }

    private function buildFilterFromRequest(): ContentListFilterDto
    {
        return new ContentListFilterDto(
            kategori: $this->nullableGet('kategori'),
            status: $this->nullableGet('status'),
            page: max(1, (int) ($this->request->getGet('page') ?? 1)),
            perPage: 10,
        );
    }

    private function buildDtoFromRequest(?string $gambarUtama, ?int $excludeId = null): BeritaDto
    {
        $judul  = trim((string) $this->request->getPost('judul'));
        $status = PublishStatus::from((string) $this->request->getPost('status'));
        $kategori = BeritaKategori::from((string) $this->request->getPost('kategori'));

        $ringkasan = trim((string) $this->request->getPost('ringkasan'));
        $konten    = trim((string) $this->request->getPost('konten'));

        return $this->beritaService->buildAdminDto(
            judul: $judul,
            kategori: $kategori,
            ringkasan: $ringkasan !== '' ? $ringkasan : null,
            konten: $konten !== '' ? $konten : null,
            status: $status,
            tanggalTerbitRaw: $this->nullablePost('tanggal_terbit'),
            gambarUtama: $gambarUtama,
            tagsRaw: $this->nullablePost('tags'),
            excludeId: $excludeId,
        );
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
        return view('admin/berita/partials/form', [
            'item'            => $this->requestFromOldInput(),
            'action'          => $this->resolveFormAction(),
            'kategoriOptions' => $this->beritaService->kategoriOptions(),
            'statusOptions'   => $this->beritaService->statusOptions(),
            'validation'      => $this->validator,
        ]);
    }

    private function formErrorResponse(string $message): string
    {
        return view('admin/berita/partials/form', [
            'item'            => $this->requestFromOldInput(),
            'action'          => $this->resolveFormAction(),
            'kategoriOptions' => $this->beritaService->kategoriOptions(),
            'statusOptions'   => $this->beritaService->statusOptions(),
            'errorMessage'    => $message,
        ]);
    }

    private function listErrorResponse(string $message): string
    {
        return view('admin/berita/partials/list_error', ['message' => $message]);
    }

    private function resolveFormAction(): string
    {
        $id = (int) $this->request->getPost('id');

        return $id > 0
            ? site_url('admin/berita/' . $id)
            : site_url('admin/berita');
    }

    private function requestFromOldInput(): Berita
    {
        $item = new Berita();
        $item->id         = (int) ($this->request->getPost('id') ?? 0);
        $item->judul      = (string) $this->request->getPost('judul');
        $item->kategori   = (string) $this->request->getPost('kategori');
        $item->ringkasan  = $this->nullablePost('ringkasan');
        $item->tags       = $this->nullablePost('tags');
        $item->konten     = $this->nullablePost('konten');
        $item->status     = (string) $this->request->getPost('status');
        $item->gambar_utama = (string) ($this->request->getPost('gambar_existing') ?? '');

        return $item;
    }
}
