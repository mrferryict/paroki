<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\DTOs\HeroSlide\HeroSlideDto;
use App\Entities\HeroSlide;
use App\Services\HeroSlideService;
use CodeIgniter\HTTP\ResponseInterface;
use DomainException;
use InvalidArgumentException;
use RuntimeException;

class HeroSlideController extends BaseController
{
    private HeroSlideService $heroSlideService;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger,
    ): void {
        parent::initController($request, $response, $logger);

        $this->heroSlideService = service('heroSlideService');
    }

    public function index(): string
    {
        $slides = $this->heroSlideService->findAllOrdered();

        if ($this->isHtmxRequest()) {
            return view('admin/hero_slide/partials/list', ['slides' => $slides]);
        }

        return view('admin/hero_slide/index', [
            'slides' => $slides,
            'title'  => 'Hero Slide',
        ]);
    }

    public function new(): string
    {
        return view('admin/hero_slide/partials/form', [
            'slide'  => null,
            'action' => site_url('admin/hero-slide'),
            'method' => 'post',
        ]);
    }

    public function create(): ResponseInterface|string
    {
        if (! $this->validate($this->createRules())) {
            return $this->validationErrorResponse();
        }

        try {
            $gambar = $this->heroSlideService->storeUploadedImage($this->request->getFile('gambar'));
            $dto    = $this->buildDtoFromRequest(gambar: $gambar);

            $this->heroSlideService->create($dto);

            session()->setFlashdata('success', 'Hero slide berhasil ditambahkan.');

            return $this->htmxRedirect(site_url('admin/hero-slide'));
        } catch (InvalidArgumentException | RuntimeException $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function edit(int $id): string
    {
        try {
            $slide = $this->heroSlideService->findById($id);
        } catch (DomainException) {
            return view('admin/hero_slide/partials/form_error', [
                'message' => 'Hero slide tidak ditemukan.',
            ]);
        }

        return view('admin/hero_slide/partials/form', [
            'slide'  => $slide,
            'action' => site_url('admin/hero-slide/' . $id),
            'method' => 'post',
        ]);
    }

    public function update(int $id): ResponseInterface|string
    {
        if (! $this->validate($this->updateRules())) {
            return $this->validationErrorResponse();
        }

        try {
            $existing = $this->heroSlideService->findById($id);
            $gambar   = (string) $existing->gambar;

            $uploaded = $this->request->getFile('gambar');

            if ($uploaded !== null && $uploaded->isValid() && ! $uploaded->hasMoved()) {
                $gambar = $this->heroSlideService->storeUploadedImage($uploaded);
            }

            $dto = $this->buildDtoFromRequest(
                gambar: $gambar,
                urutan: (int) $existing->urutan,
            );

            $this->heroSlideService->update(id: $id, dto: $dto);

            session()->setFlashdata('success', 'Hero slide berhasil diperbarui.');

            return $this->htmxRedirect(site_url('admin/hero-slide'));
        } catch (DomainException | InvalidArgumentException | RuntimeException $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function delete(int $id): ResponseInterface|string
    {
        try {
            $this->heroSlideService->delete($id);

            session()->setFlashdata('success', 'Hero slide berhasil dihapus.');

            if ($this->isHtmxRequest()) {
                return view('admin/hero_slide/partials/list', [
                    'slides' => $this->heroSlideService->findAllOrdered(),
                ]);
            }

            return redirect()->to('admin/hero-slide');
        } catch (DomainException | RuntimeException $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function moveUp(int $id): string
    {
        try {
            $this->heroSlideService->moveUp($id);
        } catch (DomainException | RuntimeException $e) {
            return view('admin/hero_slide/partials/list_error', [
                'message' => $e->getMessage(),
            ]);
        }

        return view('admin/hero_slide/partials/list', [
            'slides' => $this->heroSlideService->findAllOrdered(),
        ]);
    }

    public function moveDown(int $id): string
    {
        try {
            $this->heroSlideService->moveDown($id);
        } catch (DomainException | RuntimeException $e) {
            return view('admin/hero_slide/partials/list_error', [
                'message' => $e->getMessage(),
            ]);
        }

        return view('admin/hero_slide/partials/list', [
            'slides' => $this->heroSlideService->findAllOrdered(),
        ]);
    }

    /**
     * @return array<string, array<int, string>|string>
     */
    private function createRules(): array
    {
        return [
            'judul' => 'required|min_length[3]',
            'gambar' => 'uploaded[gambar]|max_size[gambar,5120]|mime_in[gambar,image/jpg,image/jpeg,image/png,image/webp]',
            'cta1_href' => 'permit_empty|valid_url_strict',
            'cta2_href' => 'permit_empty|valid_url_strict',
            'urutan' => 'permit_empty|is_natural',
            'is_active' => 'permit_empty|in_list[0,1]',
        ];
    }

    /**
     * @return array<string, array<int, string>|string>
     */
    private function updateRules(): array
    {
        return [
            'judul' => 'required|min_length[3]',
            'gambar' => 'if_exist|max_size[gambar,5120]|mime_in[gambar,image/jpg,image/jpeg,image/png,image/webp]',
            'cta1_href' => 'permit_empty|valid_url_strict',
            'cta2_href' => 'permit_empty|valid_url_strict',
            'is_active' => 'permit_empty|in_list[0,1]',
        ];
    }

    private function buildDtoFromRequest(string $gambar, int $urutan = 0): HeroSlideDto
    {
        $eyebrow = trim((string) $this->request->getPost('eyebrow'));
        $subjudul = trim((string) $this->request->getPost('subjudul'));

        return new HeroSlideDto(
            eyebrow: $eyebrow !== '' ? $eyebrow : null,
            judul: trim((string) $this->request->getPost('judul')),
            subjudul: $subjudul !== '' ? $subjudul : null,
            cta1Label: $this->nullablePost('cta1_label'),
            cta1Href: $this->nullablePost('cta1_href'),
            cta2Label: $this->nullablePost('cta2_label'),
            cta2Href: $this->nullablePost('cta2_href'),
            gambar: $gambar,
            urutan: $urutan > 0 ? $urutan : (int) ($this->request->getPost('urutan') ?? 0),
            isActive: $this->request->getPost('is_active') === '1',
        );
    }

    private function nullablePost(string $field): ?string
    {
        $value = trim((string) $this->request->getPost($field));

        return $value !== '' ? $value : null;
    }

    private function validationErrorResponse(): string
    {
        return view('admin/hero_slide/partials/form', [
            'slide'      => $this->requestFromOldInput(),
            'action'     => $this->resolveFormAction(),
            'method'     => 'post',
            'validation' => $this->validator,
        ]);
    }

    private function errorResponse(string $message): string
    {
        return view('admin/hero_slide/partials/form_error', [
            'message' => $message,
        ]);
    }

    private function resolveFormAction(): string
    {
        $id = (int) $this->request->getPost('id');

        if ($id > 0) {
            return site_url('admin/hero-slide/' . $id);
        }

        return site_url('admin/hero-slide');
    }

    private function requestFromOldInput(): HeroSlide
    {
        $slide = new HeroSlide();
        $slide->id = (int) ($this->request->getPost('id') ?? 0);
        $slide->eyebrow = $this->nullablePost('eyebrow');
        $slide->judul = (string) $this->request->getPost('judul');
        $slide->subjudul = $this->nullablePost('subjudul');
        $slide->cta1_label = $this->nullablePost('cta1_label');
        $slide->cta1_href = $this->nullablePost('cta1_href');
        $slide->cta2_label = $this->nullablePost('cta2_label');
        $slide->cta2_href = $this->nullablePost('cta2_href');
        $slide->gambar = (string) ($this->request->getPost('gambar_existing') ?? '');
        $slide->urutan = (int) ($this->request->getPost('urutan') ?? 0);
        $slide->is_active = $this->request->getPost('is_active') === '1';

        return $slide;
    }
}
