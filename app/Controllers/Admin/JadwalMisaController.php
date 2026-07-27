<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\DTOs\JadwalMisa\JadwalMisaDto;
use App\Entities\JadwalMisa;
use App\Services\JadwalMisaService;
use CodeIgniter\HTTP\ResponseInterface;
use DomainException;
use RuntimeException;

class JadwalMisaController extends BaseController
{
    private JadwalMisaService $jadwalMisaService;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger,
    ): void {
        parent::initController($request, $response, $logger);

        $this->jadwalMisaService = service('jadwalMisaService');
    }

    public function index(): string
    {
        $items = $this->jadwalMisaService->findAllOrdered();

        if ($this->isHtmxRequest()) {
            return view('admin/jadwal_misa/partials/list', [
                'items'        => $items,
                'jenisOptions' => $this->jadwalMisaService->jenisOptions(),
            ]);
        }

        return view('admin/jadwal_misa/index', [
            'items'        => $items,
            'title'        => 'Jadwal Misa',
            'jenisOptions' => $this->jadwalMisaService->jenisOptions(),
        ]);
    }

    public function new(): string
    {
        return view('admin/jadwal_misa/partials/form', [
            'item'         => null,
            'action'       => site_url('admin/jadwal-misa'),
            'jenisOptions' => $this->jadwalMisaService->jenisOptions(),
        ]);
    }

    public function create(): ResponseInterface|string
    {
        if (! $this->validate($this->formRules())) {
            return $this->validationErrorResponse();
        }

        try {
            $this->jadwalMisaService->create($this->buildDtoFromRequest());

            session()->setFlashdata('success', 'Jadwal misa berhasil ditambahkan.');

            return $this->htmxRedirect(site_url('admin/jadwal-misa'));
        } catch (DomainException | RuntimeException $e) {
            return $this->formErrorResponse($e->getMessage());
        }
    }

    public function edit(int $id): string
    {
        try {
            $item = $this->jadwalMisaService->findById($id);
        } catch (DomainException) {
            return $this->formErrorResponse('Jadwal misa tidak ditemukan.');
        }

        return view('admin/jadwal_misa/partials/form', [
            'item'         => $item,
            'action'       => site_url('admin/jadwal-misa/' . $id),
            'jenisOptions' => $this->jadwalMisaService->jenisOptions(),
        ]);
    }

    public function update(int $id): ResponseInterface|string
    {
        if (! $this->validate($this->formRules())) {
            return $this->validationErrorResponse();
        }

        try {
            $existing = $this->jadwalMisaService->findById($id);
            $dto      = $this->buildDtoFromRequest(urutan: (int) $existing->urutan);

            $this->jadwalMisaService->update(id: $id, dto: $dto);

            session()->setFlashdata('success', 'Jadwal misa berhasil diperbarui.');

            return $this->htmxRedirect(site_url('admin/jadwal-misa'));
        } catch (DomainException | RuntimeException $e) {
            return $this->formErrorResponse($e->getMessage());
        }
    }

    public function delete(int $id): ResponseInterface|string
    {
        try {
            $this->jadwalMisaService->delete($id);

            session()->setFlashdata('success', 'Jadwal misa berhasil dihapus.');

            return view('admin/jadwal_misa/partials/list', [
                'items'        => $this->jadwalMisaService->findAllOrdered(),
                'jenisOptions' => $this->jadwalMisaService->jenisOptions(),
            ]);
        } catch (DomainException | RuntimeException $e) {
            return $this->listErrorResponse($e->getMessage());
        }
    }

    public function moveUp(int $id): string
    {
        try {
            $this->jadwalMisaService->moveUp($id);
        } catch (DomainException | RuntimeException $e) {
            return $this->listErrorResponse($e->getMessage());
        }

        return view('admin/jadwal_misa/partials/list', [
            'items'        => $this->jadwalMisaService->findAllOrdered(),
                'jenisOptions' => $this->jadwalMisaService->jenisOptions(),
        ]);
    }

    public function moveDown(int $id): string
    {
        try {
            $this->jadwalMisaService->moveDown($id);
        } catch (DomainException | RuntimeException $e) {
            return $this->listErrorResponse($e->getMessage());
        }

        return view('admin/jadwal_misa/partials/list', [
            'items'        => $this->jadwalMisaService->findAllOrdered(),
                'jenisOptions' => $this->jadwalMisaService->jenisOptions(),
        ]);
    }

    /**
     * @return array<string, array<int, string>|string>
     */
    private function formRules(): array
    {
        return [
            'jenis'      => 'required|in_list[harian,mingguan,jumat_pertama,khusus]',
            'hari_label' => 'required|min_length[2]|max_length[100]',
            'jam'        => 'required|regex_match[/^\d{2}:\d{2}(:\d{2})?$/]',
            'catatan'    => 'permit_empty|max_length[2000]',
            'urutan'     => 'permit_empty|is_natural',
            'is_active'  => 'permit_empty|in_list[0,1]',
        ];
    }

    private function buildDtoFromRequest(int $urutan = 0): JadwalMisaDto
    {
        $catatan = trim((string) $this->request->getPost('catatan'));

        return new JadwalMisaDto(
            jenis: (string) $this->request->getPost('jenis'),
            hariLabel: trim((string) $this->request->getPost('hari_label')),
            jam: trim((string) $this->request->getPost('jam')),
            catatan: $catatan !== '' ? $catatan : null,
            urutan: $urutan > 0 ? $urutan : (int) ($this->request->getPost('urutan') ?? 0),
            isActive: $this->request->getPost('is_active') === '1',
        );
    }

    private function validationErrorResponse(): string
    {
        return view('admin/jadwal_misa/partials/form', [
            'item'         => $this->requestFromOldInput(),
            'action'       => $this->resolveFormAction(),
            'jenisOptions' => $this->jadwalMisaService->jenisOptions(),
            'validation'   => $this->validator,
        ]);
    }

    private function formErrorResponse(string $message): string
    {
        return view('admin/jadwal_misa/partials/form_error', ['message' => $message]);
    }

    private function listErrorResponse(string $message): string
    {
        return view('admin/jadwal_misa/partials/list_error', ['message' => $message]);
    }

    private function resolveFormAction(): string
    {
        $id = (int) $this->request->getPost('id');

        return $id > 0
            ? site_url('admin/jadwal-misa/' . $id)
            : site_url('admin/jadwal-misa');
    }

    private function requestFromOldInput(): JadwalMisa
    {
        $item = new JadwalMisa();
        $item->id         = (int) ($this->request->getPost('id') ?? 0);
        $item->jenis      = (string) $this->request->getPost('jenis');
        $item->hari_label = (string) $this->request->getPost('hari_label');
        $item->jam        = (string) $this->request->getPost('jam');
        $item->catatan    = trim((string) $this->request->getPost('catatan')) ?: null;
        $item->urutan     = (int) ($this->request->getPost('urutan') ?? 0);
        $item->is_active  = $this->request->getPost('is_active') === '1';

        return $item;
    }
}
