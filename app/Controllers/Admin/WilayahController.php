<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\DTOs\Wilayah\WilayahDto;
use App\Entities\Wilayah;
use App\Services\WilayahService;
use CodeIgniter\HTTP\ResponseInterface;
use DomainException;
use RuntimeException;

class WilayahController extends BaseController
{
    private WilayahService $wilayahService;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger,
    ): void {
        parent::initController($request, $response, $logger);

        $this->wilayahService = service('wilayahService');
    }

    public function index(): string
    {
        $rows = $this->wilayahService->findAllForAdminTable();

        if ($this->isHtmxRequest()) {
            return view('admin/wilayah/partials/table', ['rows' => $rows]);
        }

        return view('admin/wilayah/index', [
            'rows'  => $rows,
            'title' => 'Wilayah & Lingkungan',
        ]);
    }

    public function show(int $id): string
    {
        try {
            $detail = $this->wilayahService->getDetail($id);
        } catch (DomainException | RuntimeException) {
            return view('admin/wilayah/partials/detail_error', [
                'message' => 'Wilayah tidak ditemukan.',
            ]);
        }

        return view('admin/wilayah/show', [
            'detail' => $detail,
            'title'  => 'Detail Wilayah',
        ]);
    }

    public function new(): string
    {
        return view('admin/wilayah/partials/form', [
            'item'   => null,
            'action' => site_url('admin/wilayah'),
        ]);
    }

    public function create(): ResponseInterface|string
    {
        if (! $this->validate($this->formRules(isCreate: true))) {
            return $this->validationErrorResponse();
        }

        try {
            $this->wilayahService->create($this->buildDtoFromRequest());

            session()->setFlashdata('success', 'Wilayah berhasil ditambahkan.');

            return $this->htmxRedirect(site_url('admin/wilayah'));
        } catch (DomainException | RuntimeException $e) {
            return $this->formErrorResponse($e->getMessage());
        }
    }

    public function edit(int $id): string
    {
        try {
            $withLingkungan = $this->wilayahService->getWithLingkungan($id);
        } catch (DomainException) {
            return $this->formErrorResponse('Wilayah tidak ditemukan.');
        }

        return view('admin/wilayah/partials/form', [
            'item'   => $withLingkungan->wilayah,
            'action' => site_url('admin/wilayah/' . $id),
        ]);
    }

    public function update(int $id): ResponseInterface|string
    {
        if (! $this->validate($this->formRules(isCreate: false))) {
            return $this->validationErrorResponse();
        }

        try {
            $this->wilayahService->update($id, $this->buildDtoFromRequest());

            session()->setFlashdata('success', 'Wilayah berhasil diperbarui.');

            return $this->htmxRedirect(site_url('admin/wilayah'));
        } catch (DomainException | RuntimeException $e) {
            return $this->formErrorResponse($e->getMessage());
        }
    }

    public function delete(int $id): ResponseInterface|string
    {
        try {
            $this->wilayahService->delete($id);

            session()->setFlashdata('success', 'Wilayah berhasil dihapus.');

            return view('admin/wilayah/partials/table', [
                'rows' => $this->wilayahService->findAllForAdminTable(),
            ]);
        } catch (DomainException | RuntimeException $e) {
            return $this->listErrorResponse($e->getMessage());
        }
    }

    /**
     * @return array<string, array<int, string>|string>
     */
    private function formRules(bool $isCreate): array
    {
        $rules = [
            'nama'       => 'required|min_length[2]|max_length[255]',
            'ketua_nama' => 'required|min_length[2]|max_length[255]',
        ];

        if ($isCreate) {
            $rules['ketua_kontak'] = 'required|min_length[8]|max_length[20]';
        } else {
            $rules['ketua_kontak'] = 'permit_empty|min_length[8]|max_length[20]';
        }

        return $rules;
    }

    private function buildDtoFromRequest(): WilayahDto
    {
        return new WilayahDto(
            nama: trim((string) $this->request->getPost('nama')),
            ketuaNama: trim((string) $this->request->getPost('ketua_nama')),
            ketuaKontak: trim((string) $this->request->getPost('ketua_kontak')),
        );
    }

    private function validationErrorResponse(): string
    {
        return view('admin/wilayah/partials/form', [
            'item'       => $this->requestFromOldInput(),
            'action'     => $this->resolveFormAction(),
            'validation' => $this->validator,
        ]);
    }

    private function formErrorResponse(string $message): string
    {
        return view('admin/wilayah/partials/form_error', ['message' => $message]);
    }

    private function listErrorResponse(string $message): string
    {
        return view('admin/wilayah/partials/list_error', ['message' => $message]);
    }

    private function resolveFormAction(): string
    {
        $id = (int) $this->request->getPost('id');

        return $id > 0
            ? site_url('admin/wilayah/' . $id)
            : site_url('admin/wilayah');
    }

    private function requestFromOldInput(): Wilayah
    {
        $item = new Wilayah();
        $item->id         = (int) ($this->request->getPost('id') ?? 0);
        $item->nama       = (string) $this->request->getPost('nama');
        $item->ketua_nama = (string) $this->request->getPost('ketua_nama');

        return $item;
    }
}
