<?php

declare(strict_types=1);

namespace Tests\Unit\Controllers\Admin;

use App\Controllers\Admin\PendaftaranController;
use App\DTOs\Pendaftaran\PaginatedPendaftaranListDto;
use App\DTOs\Pendaftaran\PendaftaranListFilterDto;
use App\DTOs\Pendaftaran\PendaftaranListItemDto;
use App\Enums\PendaftaranStatus;
use App\Libraries\PiiCipher;
use App\Services\PendaftaranService;
use CodeIgniter\I18n\Time;
use CodeIgniter\Pager\PagerInterface;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTestTrait;
use Config\Services;

/**
 * @internal
 */
final class PendaftaranAdminListControllerTest extends CIUnitTestCase
{
    use ControllerTestTrait;

    private string $plaintextWhatsapp;

    private string $whatsappCipher;

    protected function setUp(): void
    {
        parent::setUp();

        if (! extension_loaded('sodium')) {
            $this->markTestSkipped('The sodium extension is required for Pendaftaran admin list tests.');
        }

        $this->plaintextWhatsapp = '081234567890';
        $this->whatsappCipher    = (new PiiCipher(
            sodium_bin2base64(random_bytes(32), \SODIUM_BASE64_VARIANT_ORIGINAL),
        ))->encrypt($this->plaintextWhatsapp);
    }

    protected function tearDown(): void
    {
        Services::resetSingle('pendaftaranService');

        parent::tearDown();
    }

    public function testIndexResponseNeverContainsWhatsappPii(): void
    {
        Services::injectMock('pendaftaranService', $this->createMockPendaftaranService());

        $result = $this->controller(PendaftaranController::class)
            ->execute('index');

        $this->assertStringNotContainsString('whatsapp_cipher', $result->getBody());
        $this->assertStringNotContainsString('whatsapp_hash', $result->getBody());
        $this->assertStringNotContainsString($this->plaintextWhatsapp, $result->getBody());
        $this->assertStringNotContainsString($this->whatsappCipher, $result->getBody());
    }

    public function testIndexHtmxListPartialNeverContainsWhatsappPii(): void
    {
        Services::injectMock('pendaftaranService', $this->createMockPendaftaranService());

        $request = service('incomingrequest', config(\Config\App::class), false);
        $request->setHeader('HX-Request', 'true');

        $result = $this->withRequest($request)
            ->controller(PendaftaranController::class)
            ->execute('index');

        $this->assertStringNotContainsString('whatsapp_cipher', $result->getBody());
        $this->assertStringNotContainsString('whatsapp_hash', $result->getBody());
        $this->assertStringNotContainsString($this->plaintextWhatsapp, $result->getBody());
        $this->assertStringNotContainsString($this->whatsappCipher, $result->getBody());
    }

    private function createMockPendaftaranService(): PendaftaranService
    {
        $pager = $this->createMock(PagerInterface::class);
        $pager->method('getPageCount')->willReturn(1);
        $pager->method('getCurrentPage')->willReturn(1);
        $pager->method('getDetails')->willReturn(['total' => 1]);

        $listResult = new PaginatedPendaftaranListDto(
            items: [
                new PendaftaranListItemDto(
                    id: 1,
                    namaLengkap: 'Budi Santoso',
                    sakramenNama: 'Baptis',
                    status: PendaftaranStatus::Baru,
                    createdAt: Time::parse('2026-07-27 10:00:00'),
                ),
            ],
            pager: $pager,
        );

        $service = $this->createMock(PendaftaranService::class);
        $service->method('findPaginatedForAdmin')->willReturn($listResult);
        $service->method('statusOptions')->willReturn(PendaftaranStatus::options());

        return $service;
    }
}
