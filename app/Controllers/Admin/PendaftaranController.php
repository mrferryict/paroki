<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\DTOs\Pendaftaran\PendaftaranListFilterDto;
use App\Enums\PendaftaranStatus;
use App\Services\PendaftaranService;
use CodeIgniter\HTTP\ResponseInterface;
use DomainException;
use RuntimeException;

class PendaftaranController extends BaseController
{
    private PendaftaranService $pendaftaranService;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger,
    ): void {
        parent::initController($request, $response, $logger);

        $this->pendaftaranService = service('pendaftaranService');
    }

    public function index(): string
    {
        $filter = $this->buildFilterFromRequest();
        $result = $this->pendaftaranService->findPaginatedForAdmin($filter);

        $viewData = [
            'result'        => $result,
            'filter'        => $filter,
            'statusOptions' => $this->pendaftaranService->statusOptions(),
        ];

        if ($this->isHtmxRequest()) {
            return view('admin/pendaftaran/partials/list', $viewData);
        }

        return view('admin/pendaftaran/index', array_merge($viewData, [
            'title' => 'Pendaftaran',
        ]));
    }

    public function show(int $id): string
    {
        try {
            $detail = $this->pendaftaranService->getDetail($id);
        } catch (DomainException | RuntimeException) {
            return view('admin/pendaftaran/partials/detail_error', [
                'message' => 'Pendaftaran tidak ditemukan.',
            ]);
        }

        return view('admin/pendaftaran/show', [
            'detail'              => $detail,
            'title'               => 'Detail Pendaftaran',
            'statusOptions'       => $this->pendaftaranService->statusOptions(),
            'allowedNextStatuses' => $this->pendaftaranService->getAllowedNextStatuses(
                $detail->pendaftaran->status instanceof PendaftaranStatus
                    ? $detail->pendaftaran->status
                    : PendaftaranStatus::from((string) $detail->pendaftaran->status),
            ),
        ]);
    }

    public function updateStatus(int $id): ResponseInterface|string
    {
        if (! $this->validate([
            'status' => 'required|in_list[baru,diproses,selesai,ditolak]',
        ])) {
            session()->setFlashdata('error', 'Status tidak valid.');

            return redirect()->to('admin/pendaftaran/' . $id);
        }

        try {
            $this->pendaftaranService->updateStatus(
                $id,
                PendaftaranStatus::from((string) $this->request->getPost('status')),
            );

            session()->setFlashdata('success', 'Status pendaftaran berhasil diperbarui.');
        } catch (DomainException | RuntimeException $e) {
            session()->setFlashdata('error', $e->getMessage());
        }

        return redirect()->to('admin/pendaftaran/' . $id);
    }

    private function buildFilterFromRequest(): PendaftaranListFilterDto
    {
        return new PendaftaranListFilterDto(
            status: $this->nullableGet('status'),
            page: max(1, (int) ($this->request->getGet('page') ?? 1)),
            perPage: 15,
        );
    }

    private function nullableGet(string $field): ?string
    {
        $value = trim((string) $this->request->getGet($field));

        return $value !== '' ? $value : null;
    }
}
