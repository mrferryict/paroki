<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\DokumenService;
use CodeIgniter\HTTP\ResponseInterface;
use DomainException;

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

    public function download(int $id): ResponseInterface
    {
        try {
            $download = $this->dokumenService->resolveDownload($id);
        } catch (DomainException) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return $this->response->download($download->fullPath, null)->setFileName($download->clientName);
    }
}
