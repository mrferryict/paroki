<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\DTOs\Lingkungan\LingkunganDto;
use App\Entities\Lingkungan;
use App\Services\LingkunganService;
use App\Services\WilayahService;
use CodeIgniter\HTTP\ResponseInterface;
use DomainException;
use RuntimeException;

class LingkunganController extends BaseController
{
    private LingkunganService $lingkunganService;

    private WilayahService $wilayahService;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger,
    ): void {
        parent::initController($request, $response, $logger);

        $this->lingkunganService = service('lingkunganService');
        $this->wilayahService    = service('wilayahService');
    }

    public function index(int $wilayahId): ResponseInterface
    {
        return redirect()->to(site_url('admin/wilayah'));
    }

    public function show(int $wilayahId, int $id): string
    {
        try {
            $detail  = $this->lingkunganService->getDetailForWilayah(wilayahId: $wilayahId, id: $id);
            $wilayah = $this->wilayahService->getWithLingkungan($wilayahId)->wilayah;
        } catch (DomainException | RuntimeException) {
            return view('admin/lingkungan/partials/detail_error', [
                'message' => 'Lingkungan tidak ditemukan.',
            ]);
        }

        return view('admin/lingkungan/show', [
            'wilayah' => $wilayah,
            'detail'  => $detail,
            'title'   => 'Detail Lingkungan',
        ]);
    }

    public function new(int $wilayahId): string
    {
        try {
            $wilayah = $this->wilayahService->getWithLingkungan($wilayahId)->wilayah;
        } catch (DomainException) {
            return $this->formErrorResponse('Wilayah tidak ditemukan.');
        }

        return view('admin/lingkungan/partials/form', [
            'wilayah' => $wilayah,
            'item'    => null,
            'action'  => site_url('admin/wilayah/' . $wilayahId . '/lingkungan'),
        ]);
    }

    public function create(int $wilayahId): ResponseInterface|string
    {
        if (! $this->validate($this->formRules())) {
            return $this->validationErrorResponse($wilayahId);
        }

        try {
            $this->lingkunganService->create($this->buildDtoFromRequest(wilayahId: $wilayahId));

            session()->setFlashdata('success', 'Lingkungan berhasil ditambahkan.');

            return $this->htmxRedirect(site_url('admin/wilayah'));
        } catch (DomainException | RuntimeException $e) {
            return $this->formErrorResponse($e->getMessage());
        }
    }

    public function edit(int $wilayahId, int $id): string
    {
        try {
            $detail  = $this->lingkunganService->getDetailForWilayah(wilayahId: $wilayahId, id: $id);
            $wilayah = $this->wilayahService->getWithLingkungan($wilayahId)->wilayah;
        } catch (DomainException) {
            return $this->formErrorResponse('Lingkungan tidak ditemukan.');
        }

        return view('admin/lingkungan/partials/form', [
            'wilayah' => $wilayah,
            'item'    => $detail->lingkungan,
            'action'  => site_url('admin/wilayah/' . $wilayahId . '/lingkungan/' . $id),
        ]);
    }

    public function update(int $wilayahId, int $id): ResponseInterface|string
    {
        if (! $this->validate($this->formRules())) {
            return $this->validationErrorResponse($wilayahId);
        }

        try {
            $this->lingkunganService->updateForWilayah(
                wilayahId: $wilayahId,
                id: $id,
                dto: $this->buildDtoFromRequest(wilayahId: $wilayahId),
            );

            session()->setFlashdata('success', 'Lingkungan berhasil diperbarui.');

            return $this->htmxRedirect(site_url('admin/wilayah'));
        } catch (DomainException | RuntimeException $e) {
            return $this->formErrorResponse($e->getMessage());
        }
    }

    public function delete(int $wilayahId, int $id): ResponseInterface|string
    {
        try {
            $this->lingkunganService->deleteForWilayah(wilayahId: $wilayahId, id: $id);

            session()->setFlashdata('success', 'Lingkungan berhasil dihapus.');

            return view('admin/wilayah/partials/table', [
                'rows' => $this->wilayahService->findAllForAdminTable(),
            ]);
        } catch (DomainException | RuntimeException $e) {
            return view('admin/lingkungan/partials/list_error', ['message' => $e->getMessage()]);
        }
    }

    /**
     * @return array<string, array<int, string>|string>
     */
    private function formRules(): array
    {
        return [
            'nama'         => 'required|min_length[2]|max_length[255]',
            'ketua_nama'   => 'required|min_length[2]|max_length[255]',
            'ketua_kontak' => 'permit_empty|min_length[8]|max_length[20]',
        ];
    }

    private function buildDtoFromRequest(int $wilayahId): LingkunganDto
    {
        $kontak = trim((string) $this->request->getPost('ketua_kontak'));

        return new LingkunganDto(
            wilayahId: $wilayahId,
            nama: trim((string) $this->request->getPost('nama')),
            ketuaNama: trim((string) $this->request->getPost('ketua_nama')),
            ketuaKontak: $kontak !== '' ? $kontak : null,
        );
    }

    private function validationErrorResponse(int $wilayahId): string
    {
        try {
            $wilayah = $this->wilayahService->getWithLingkungan($wilayahId)->wilayah;
        } catch (DomainException) {
            return $this->formErrorResponse('Wilayah tidak ditemukan.');
        }

        return view('admin/lingkungan/partials/form', [
            'wilayah'    => $wilayah,
            'item'       => $this->requestFromOldInput(wilayahId: $wilayahId),
            'action'     => $this->resolveFormAction(wilayahId: $wilayahId),
            'validation' => $this->validator,
        ]);
    }

    private function formErrorResponse(string $message): string
    {
        return view('admin/lingkungan/partials/form_error', ['message' => $message]);
    }

    private function resolveFormAction(int $wilayahId): string
    {
        $id = (int) $this->request->getPost('id');

        return $id > 0
            ? site_url('admin/wilayah/' . $wilayahId . '/lingkungan/' . $id)
            : site_url('admin/wilayah/' . $wilayahId . '/lingkungan');
    }

    private function requestFromOldInput(int $wilayahId): Lingkungan
    {
        $item = new Lingkungan();
        $item->id         = (int) ($this->request->getPost('id') ?? 0);
        $item->wilayah_id = $wilayahId;
        $item->nama       = (string) $this->request->getPost('nama');
        $item->ketua_nama = (string) $this->request->getPost('ketua_nama');

        return $item;
    }
}
