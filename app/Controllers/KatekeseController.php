<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Enums\ArtikelKategori;
use App\Services\ArtikelService;
use CodeIgniter\Exceptions\PageNotFoundException;
use DomainException;

class KatekeseController extends BaseController
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
        if ($kategori !== null && $kategori !== '' && ArtikelKategori::tryFromString($kategori) === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $page = max(1, (int) $this->request->getGet('page'));

        $result = $this->artikelService->findPublishedPaginated(
            kategori: $kategori !== null && $kategori !== '' ? $kategori : null,
            page: $page,
        );

        $items = array_map(
            fn ($item) => $this->artikelService->mapForPublicCard($item),
            $result->items,
        );

        $activeKategori = $kategori ?? '';

        return view('katekese/index', [
            'title'           => 'Katekese & Renungan',
            'items'           => $items,
            'pager'           => $result->pager,
            'activeKategori'  => $activeKategori,
            'kategoriOptions' => $this->artikelService->kategoriOptions(),
        ]);
    }

    public function show(string $kategori, string $slug): string
    {
        try {
            $artikel = $this->artikelService->findPublishedByKategoriAndSlug($kategori, $slug);
        } catch (DomainException) {
            throw PageNotFoundException::forPageNotFound();
        }

        $detail = $this->artikelService->mapForPublicDetail($artikel);

        return view('katekese/show', [
            'title'  => (string) ($detail['judul'] ?? 'Katekese'),
            'detail' => $detail,
        ]);
    }
}
