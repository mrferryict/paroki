<?php

declare(strict_types=1);

namespace App\Services;

use App\Entities\SiteSetting;
use App\Libraries\PublicUploadDirectory;
use App\Models\SiteSettingModel;
use CodeIgniter\HTTP\Files\UploadedFile;
use InvalidArgumentException;
use RuntimeException;

class SiteSettingService
{
    private const SETTING_ID = 1;

    private const UPLOAD_SUBDIR = 'uploads/branding';

    /** @var list<string> */
    private const ALLOWED_MIMES = [
        'image/jpeg',
        'image/png',
        'image/jpg',
    ];

    public function __construct(
        private readonly SiteSettingModel $siteSettingModel,
    ) {}

    public function get(): SiteSetting
    {
        $setting = $this->siteSettingModel->find(self::SETTING_ID);

        if ($setting === null) {
            throw new RuntimeException('Pengaturan situs belum diinisialisasi.');
        }

        return $setting;
    }

    /**
     * @return array{logoUrl: ?string, siteName: string}
     */
    public function getBranding(): array
    {
        $setting = $this->get();

        return [
            'logoUrl'  => $this->resolvePublicLogoUrl((string) ($setting->logo_path ?? '')),
            'siteName' => 'Paroki Hati Kudus Yesus',
        ];
    }

    public function updateLogo(UploadedFile $file): void
    {
        $storedPath = $this->storeUploadedLogo($file);
        $existing   = $this->get();
        $oldPath    = (string) ($existing->logo_path ?? '');

        if (! $this->siteSettingModel->update(self::SETTING_ID, ['logo_path' => $storedPath])) {
            $this->removeStoredLogo($storedPath);

            throw new RuntimeException('Gagal menyimpan logo paroki.');
        }

        if ($oldPath !== '' && $oldPath !== $storedPath) {
            $this->removeStoredLogo($oldPath);
        }
    }

    public function removeLogo(): void
    {
        $existing = $this->get();
        $oldPath  = (string) ($existing->logo_path ?? '');

        if (! $this->siteSettingModel->update(self::SETTING_ID, ['logo_path' => null])) {
            throw new RuntimeException('Gagal menghapus logo paroki.');
        }

        if ($oldPath !== '') {
            $this->removeStoredLogo($oldPath);
        }
    }

    public function storeUploadedLogo(UploadedFile $file): string
    {
        if (! $file->isValid()) {
            throw new InvalidArgumentException($file->getErrorString() ?: 'File logo tidak valid.');
        }

        $mime = $file->getMimeType();

        if ($mime === null || ! in_array($mime, self::ALLOWED_MIMES, true)) {
            throw new InvalidArgumentException('Format logo harus JPEG atau PNG.');
        }

        $targetDir = PublicUploadDirectory::ensure(self::UPLOAD_SUBDIR);

        $storedName = $file->getRandomName();

        if (! $file->move($targetDir, $storedName)) {
            throw new RuntimeException('Gagal mengunggah logo paroki.');
        }

        return self::UPLOAD_SUBDIR . '/' . $storedName;
    }

    private function resolvePublicLogoUrl(string $relativePath): ?string
    {
        if ($relativePath === '') {
            return null;
        }

        $fullPath = FCPATH . ltrim($relativePath, '/');

        if (! is_file($fullPath)) {
            return null;
        }

        return base_url($relativePath);
    }

    private function removeStoredLogo(string $relativePath): void
    {
        if ($relativePath === '') {
            return;
        }

        $fullPath = FCPATH . ltrim($relativePath, '/');

        if (is_file($fullPath)) {
            unlink($fullPath);
        }
    }
}
