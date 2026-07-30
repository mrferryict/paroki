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
        $branding = $this->siteSettingService->getBranding();

        return view('admin/pengaturan/index', [
            'title'         => 'Pengaturan Situs',
            'setting'       => $setting,
            'logoUrl'       => $branding['logoUrl'],
            'siteName'      => $branding['siteName'],
            'copyrightText' => $branding['copyrightText'],
        ]);
    }

    public function updateSiteInfo(): RedirectResponse
    {
        if (! $this->validate([
            'site_name'      => 'required|max_length[255]',
            'copyright_text' => 'required|max_length[500]',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            $this->siteSettingService->updateSiteInfo(
                siteName: (string) $this->request->getPost('site_name'),
                copyrightText: (string) $this->request->getPost('copyright_text'),
            );

            return redirect()->to(site_url('admin/pengaturan'))
                ->with('success', 'Informasi situs berhasil diperbarui.');
        } catch (InvalidArgumentException | RuntimeException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
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
