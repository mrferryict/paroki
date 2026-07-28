<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\DTOs\DewanParokiBidang\DewanParokiPenjabatDto;
use App\Entities\DewanParokiPenjabat;
use App\Services\DewanParokiBidangService;
use App\Services\DewanParokiPenjabatService;
use CodeIgniter\HTTP\ResponseInterface;
use DomainException;
use RuntimeException;

class DewanParokiPenjabatController extends BaseController
{
    private DewanParokiPenjabatService $penjabatService;

    private DewanParokiBidangService $bidangService;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger,
    ): void {
        parent::initController($request, $response, $logger);

        $this->penjabatService = service('dewanParokiPenjabatService');
        $this->bidangService   = service('dewanParokiBidangService');
    }

    public function new(int $bidangId): string
    {
        try {
            $bidang = $this->bidangService->findById($bidangId);
        } catch (DomainException) {
            return view('admin/dewan_paroki_bidang/partials/form_error', ['message' => 'Bidang DPH tidak ditemukan.']);
        }

        return view('admin/dewan_paroki_bidang/partials/penjabat_form', [
            'bidang' => $bidang,
            'item'   => null,
            'action' => site_url('admin/dewan-paroki/' . $bidangId . '/penjabat'),
        ]);
    }

    public function create(int $bidangId): ResponseInterface|string
    {
        if (! $this->validate($this->createRules())) {
            return $this->validationErrorResponse(bidangId: $bidangId);
        }

        try {
            $this->penjabatService->create($this->buildDtoFromRequest(bidangId: $bidangId));

            session()->setFlashdata('success', 'Penjabat DPH berhasil ditambahkan.');

            return $this->htmxRedirect(site_url('admin/dewan-paroki'));
        } catch (DomainException | RuntimeException $e) {
            return view('admin/dewan_paroki_bidang/partials/form_error', ['message' => $e->getMessage()]);
        }
    }

    public function edit(int $bidangId, int $id): string
    {
        try {
            $bidang = $this->bidangService->findById($bidangId);
            $item   = $this->penjabatService->findById($id);

            if ((int) $item->bidang_id !== $bidangId) {
                throw new DomainException('Penjabat tidak ditemukan.');
            }
        } catch (DomainException) {
            return view('admin/dewan_paroki_bidang/partials/form_error', ['message' => 'Penjabat tidak ditemukan.']);
        }

        return view('admin/dewan_paroki_bidang/partials/penjabat_form', [
            'bidang' => $bidang,
            'item'   => $item,
            'action' => site_url('admin/dewan-paroki/' . $bidangId . '/penjabat/' . $id),
        ]);
    }

    public function update(int $bidangId, int $id): ResponseInterface|string
    {
        if (! $this->validate($this->updateRules())) {
            return $this->validationErrorResponse(bidangId: $bidangId);
        }

        try {
            $this->penjabatService->update($id, $this->buildDtoFromRequest(bidangId: $bidangId));

            session()->setFlashdata('success', 'Penjabat DPH berhasil diperbarui.');

            return $this->htmxRedirect(site_url('admin/dewan-paroki'));
        } catch (DomainException | RuntimeException $e) {
            return view('admin/dewan_paroki_bidang/partials/form_error', ['message' => $e->getMessage()]);
        }
    }

    public function delete(int $bidangId, int $id): ResponseInterface|string
    {
        try {
            $this->penjabatService->delete(id: $id, bidangId: $bidangId);

            session()->setFlashdata('success', 'Penjabat DPH berhasil dihapus.');

            return view('admin/dewan_paroki_bidang/partials/table', [
                'rows' => $this->bidangService->findAllForAdminTable(),
            ]);
        } catch (DomainException | RuntimeException $e) {
            return view('admin/dewan_paroki_bidang/partials/list_error', ['message' => $e->getMessage()]);
        }
    }

    /**
     * @return array<string, array<int, string>|string>
     */
    private function createRules(): array
    {
        return [
            'nama'      => 'required|min_length[2]|max_length[255]',
            'whatsapp'  => 'required|min_length[8]|max_length[20]',
        ];
    }

    /**
     * @return array<string, array<int, string>|string>
     */
    private function updateRules(): array
    {
        return [
            'nama'     => 'required|min_length[2]|max_length[255]',
            'whatsapp' => 'permit_empty|min_length[8]|max_length[20]',
        ];
    }

    private function buildDtoFromRequest(int $bidangId): DewanParokiPenjabatDto
    {
        return new DewanParokiPenjabatDto(
            bidangId: $bidangId,
            nama: trim((string) $this->request->getPost('nama')),
            whatsapp: trim((string) $this->request->getPost('whatsapp')),
        );
    }

    private function validationErrorResponse(int $bidangId): string
    {
        try {
            $bidang = $this->bidangService->findById($bidangId);
        } catch (DomainException) {
            return view('admin/dewan_paroki_bidang/partials/form_error', ['message' => 'Bidang DPH tidak ditemukan.']);
        }

        return view('admin/dewan_paroki_bidang/partials/penjabat_form', [
            'bidang'     => $bidang,
            'item'       => $this->requestFromOldInput(bidangId: $bidangId),
            'action'     => $this->resolveFormAction(bidangId: $bidangId),
            'validation' => $this->validator,
        ]);
    }

    private function resolveFormAction(int $bidangId): string
    {
        $id = (int) $this->request->getPost('id');

        return $id > 0
            ? site_url('admin/dewan-paroki/' . $bidangId . '/penjabat/' . $id)
            : site_url('admin/dewan-paroki/' . $bidangId . '/penjabat');
    }

    private function requestFromOldInput(int $bidangId): DewanParokiPenjabat
    {
        $item = new DewanParokiPenjabat();
        $item->id        = (int) ($this->request->getPost('id') ?? 0);
        $item->bidang_id = $bidangId;
        $item->nama      = (string) $this->request->getPost('nama');

        return $item;
    }
}
