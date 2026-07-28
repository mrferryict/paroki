<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\SiteSettingService;
use CodeIgniter\HTTP\RedirectResponse;
use InvalidArgumentException;
use RuntimeException;

class PengaturanController extends BaseController
{
    private SiteSettingService $siteSettingService;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger,
    ): void {
        parent::initController($request, $response, $logger);

        $this->siteSettingService = service('siteSettingService');
    }

    public function index(): string
    {
        $setting = $this->siteSettingService->get();
        $logoUrl = $this->siteSettingService->getBranding()['logoUrl'];

        return view('admin/pengaturan/index', [
            'title'   => 'Pengaturan Situs',
            'setting' => $setting,
            'logoUrl' => $logoUrl,
        ]);
    }

    public function updateLogo(): RedirectResponse
    {
        if (! $this->validate([
            'logo' => 'uploaded[logo]|max_size[logo,2048]|mime_in[logo,image/jpg,image/jpeg,image/png]',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            $this->siteSettingService->updateLogo($this->request->getFile('logo'));

            return redirect()->to(site_url('admin/pengaturan'))
                ->with('success', 'Logo paroki berhasil diperbarui.');
        } catch (InvalidArgumentException | RuntimeException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function removeLogo(): RedirectResponse
    {
        try {
            $this->siteSettingService->removeLogo();

            return redirect()->to(site_url('admin/pengaturan'))
                ->with('success', 'Logo paroki berhasil dihapus.');
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
