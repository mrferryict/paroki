<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Enums\BeritaKategori;
use App\Services\BeritaService;
use CodeIgniter\Exceptions\PageNotFoundException;
use DomainException;

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
        $kategori = trim((string) $this->request->getGet('kategori'));
        $tag      = trim((string) $this->request->getGet('tag'));
        $page     = max(1, (int) $this->request->getGet('page'));

        if ($kategori !== '' && BeritaKategori::tryFromString($kategori) === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $result = $this->beritaService->findPublishedPaginated(
            kategori: $kategori !== '' ? $kategori : null,
            page: $page,
            tag: $tag !== '' ? $tag : null,
        );

        $items = array_map(
            fn ($item) => $this->beritaService->mapForPublicCard($item),
            $result->items,
        );

        return view('berita/index', [
            'title'           => 'Berita & Kegiatan',
            'items'           => $items,
            'pager'           => $result->pager,
            'activeKategori'  => $kategori,
            'activeTag'       => $tag,
            'tagOptions'      => $this->beritaService->findPublishedTags(),
            'kategoriOptions' => $this->beritaService->kategoriOptions(),
        ]);
    }

    public function show(string $slug): string
    {
        try {
            $berita = $this->beritaService->findBySlug($slug);
        } catch (DomainException) {
            throw PageNotFoundException::forPageNotFound();
        }

        $this->beritaService->incrementViewCount((int) $berita->id);

        $detail = $this->beritaService->mapForPublicDetail($berita);

        return view('berita/show', [
            'title'  => (string) ($detail['judul'] ?? 'Berita'),
            'detail' => $detail,
        ]);
    }
}
