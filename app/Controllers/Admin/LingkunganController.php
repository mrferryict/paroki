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

    public function index(int $wilayahId): string
    {
        try {
            $wilayahWithLingkungan = $this->wilayahService->getWithLingkungan($wilayahId);
        } catch (DomainException) {
            return view('admin/lingkungan/partials/list_error', [
                'message' => 'Wilayah tidak ditemukan.',
            ]);
        }

        if ($this->isHtmxRequest()) {
            return view('admin/lingkungan/partials/list', [
                'wilayah' => $wilayahWithLingkungan->wilayah,
                'items'   => $wilayahWithLingkungan->lingkungan,
            ]);
        }

        return view('admin/lingkungan/index', [
            'wilayah' => $wilayahWithLingkungan->wilayah,
            'items'   => $wilayahWithLingkungan->lingkungan,
            'title'   => 'Lingkungan — ' . $wilayahWithLingkungan->wilayah->nama,
        ]);
    }

    public function show(int $wilayahId, int $id): string
    {
        try {
            $detail = $this->lingkunganService->getDetail($id);
            $wilayah = $this->wilayahService->getWithLingkungan($wilayahId)->wilayah;
        } catch (DomainException | RuntimeException) {
            return view('admin/lingkungan/partials/detail_error', [
                'message' => 'Lingkungan tidak ditemukan.',
            ]);
        }

        if ((int) $detail->lingkungan->wilayah_id !== $wilayahId) {
            return view('admin/lingkungan/partials/detail_error', [
                'message' => 'Lingkungan tidak ditemukan di wilayah ini.',
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

            return $this->htmxRedirect(site_url('admin/wilayah/' . $wilayahId . '/lingkungan'));
        } catch (DomainException | RuntimeException $e) {
            return $this->formErrorResponse($e->getMessage());
        }
    }

    public function edit(int $wilayahId, int $id): string
    {
        try {
            $detail  = $this->lingkunganService->getDetail($id);
            $wilayah = $this->wilayahService->getWithLingkungan($wilayahId)->wilayah;
        } catch (DomainException) {
            return $this->formErrorResponse('Lingkungan tidak ditemukan.');
        }

        if ((int) $detail->lingkungan->wilayah_id !== $wilayahId) {
            return $this->formErrorResponse('Lingkungan tidak ditemukan di wilayah ini.');
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
            $this->lingkunganService->update($id, $this->buildDtoFromRequest(wilayahId: $wilayahId));

            session()->setFlashdata('success', 'Lingkungan berhasil diperbarui.');

            return $this->htmxRedirect(site_url('admin/wilayah/' . $wilayahId . '/lingkungan'));
        } catch (DomainException | RuntimeException $e) {
            return $this->formErrorResponse($e->getMessage());
        }
    }

    public function delete(int $wilayahId, int $id): ResponseInterface|string
    {
        try {
            $detail = $this->lingkunganService->getDetail($id);

            if ((int) $detail->lingkungan->wilayah_id !== $wilayahId) {
                throw new DomainException('Lingkungan tidak ditemukan di wilayah ini.');
            }

            $this->lingkunganService->delete($id);

            session()->setFlashdata('success', 'Lingkungan berhasil dihapus.');

            $wilayah = $this->wilayahService->getWithLingkungan($wilayahId);

            return view('admin/lingkungan/partials/list', [
                'wilayah' => $wilayah->wilayah,
                'items'   => $wilayah->lingkungan,
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
