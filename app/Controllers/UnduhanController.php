<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\DokumenKategoriService;
use App\Services\DokumenService;
use CodeIgniter\Exceptions\PageNotFoundException;

class UnduhanController extends BaseController
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
        $kategoriRaw = trim((string) $this->request->getGet('kategori'));

        if ($kategoriRaw !== '' && ! $this->dokumenKategoriService->isActiveSlug($kategoriRaw)) {
            throw PageNotFoundException::forPageNotFound();
        }

        $items = array_map(
            fn ($item) => $this->dokumenService->mapForPublic($item),
            $this->dokumenService->findAllForPublic(
                kategori: $kategoriRaw !== '' ? $kategoriRaw : null,
            ),
        );

        return view('unduhan/index', [
            'title'           => 'Unduhan',
            'items'           => $items,
            'activeKategori'  => $kategoriRaw,
            'kategoriOptions' => $this->dokumenService->kategoriOptions(),
        ]);
    }
}
