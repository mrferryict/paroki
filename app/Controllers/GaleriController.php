<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\GaleriService;
use CodeIgniter\Exceptions\PageNotFoundException;
use DomainException;

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
        return view('galeri/index', [
            'title'  => 'Galeri',
            'events' => $this->galeriService->findPublishedForPublic(),
        ]);
    }

    public function show(string $slug): string
    {
        try {
            $event = $this->galeriService->findPublishedEventBySlug($slug);
        } catch (DomainException) {
            throw PageNotFoundException::forPageNotFound();
        }

        $this->galeriService->incrementEventViewCount((int) $event['id']);

        return view('galeri/show', [
            'title' => (string) ($event['judul'] ?? 'Galeri'),
            'event' => $event,
        ]);
    }
}
