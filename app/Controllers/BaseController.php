<?php

declare(strict_types=1);

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 */
abstract class BaseController extends Controller
{
    /**
     * @var list<string>
     */
    protected $helpers = ['form', 'url'];

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);
    }

    protected function isHtmxRequest(): bool
    {
        return $this->request->getHeaderLine('HX-Request') === 'true';
    }

    protected function htmxRedirect(string $url): ResponseInterface
    {
        return $this->response->setHeader('HX-Redirect', $url);
    }
}
