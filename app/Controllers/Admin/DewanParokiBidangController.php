<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\DTOs\DewanParokiBidang\DewanParokiBidangDto;
use App\Entities\DewanParokiBidang;
use App\Services\DewanParokiBidangService;
use CodeIgniter\HTTP\ResponseInterface;
use DomainException;
use RuntimeException;

class DewanParokiBidangController extends BaseController
{
    private DewanParokiBidangService $dewanParokiBidangService;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger,
    ): void {
        parent::initController($request, $response, $logger);

        $this->dewanParokiBidangService = service('dewanParokiBidangService');
    }

    public function index(): string
    {
        $items = $this->dewanParokiBidangService->findAllOrdered();

        if ($this->isHtmxRequest()) {
            return view('admin/dewan_paroki_bidang/partials/list', [
                'items'       => $items,
                'kodeOptions' => $this->dewanParokiBidangService->kodeOptions(),
            ]);
        }

        return view('admin/dewan_paroki_bidang/index', [
            'items'       => $items,
            'title'       => 'Dewan Paroki — Bidang DPH',
            'kodeOptions' => $this->dewanParokiBidangService->kodeOptions(),
        ]);
    }

    public function new(): string
    {
        return view('admin/dewan_paroki_bidang/partials/form', [
            'item'        => null,
            'action'      => site_url('admin/dewan-paroki'),
            'kodeOptions' => $this->dewanParokiBidangService->kodeOptions(),
        ]);
    }

    public function create(): ResponseInterface|string
    {
        if (! $this->validate($this->formRules(isCreate: true))) {
            return $this->validationErrorResponse();
        }

        try {
            $this->dewanParokiBidangService->create($this->buildDtoFromRequest());

            session()->setFlashdata('success', 'Bidang DPH berhasil ditambahkan.');

            return $this->htmxRedirect(site_url('admin/dewan-paroki'));
        } catch (DomainException | RuntimeException $e) {
            return $this->formErrorResponse($e->getMessage());
        }
    }

    public function edit(int $id): string
    {
        try {
            $item = $this->dewanParokiBidangService->findById($id);
        } catch (DomainException) {
            return $this->formErrorResponse('Bidang DPH tidak ditemukan.');
        }

        return view('admin/dewan_paroki_bidang/partials/form', [
            'item'        => $item,
            'action'      => site_url('admin/dewan-paroki/' . $id),
            'kodeOptions' => $this->dewanParokiBidangService->kodeOptions(),
        ]);
    }

    public function update(int $id): ResponseInterface|string
    {
        if (! $this->validate($this->formRules(isCreate: false))) {
            return $this->validationErrorResponse();
        }

        try {
            $existing = $this->dewanParokiBidangService->findById($id);
            $dto      = $this->buildDtoFromRequest(urutan: (int) $existing->urutan);

            $this->dewanParokiBidangService->update(id: $id, dto: $dto);

            session()->setFlashdata('success', 'Bidang DPH berhasil diperbarui.');

            return $this->htmxRedirect(site_url('admin/dewan-paroki'));
        } catch (DomainException | RuntimeException $e) {
            return $this->formErrorResponse($e->getMessage());
        }
    }

    public function delete(int $id): ResponseInterface|string
    {
        try {
            $this->dewanParokiBidangService->delete($id);

            session()->setFlashdata('success', 'Bidang DPH berhasil dihapus.');

            return view('admin/dewan_paroki_bidang/partials/list', [
                'items'       => $this->dewanParokiBidangService->findAllOrdered(),
                'kodeOptions' => $this->dewanParokiBidangService->kodeOptions(),
            ]);
        } catch (DomainException | RuntimeException $e) {
            return $this->listErrorResponse($e->getMessage());
        }
    }

    public function moveUp(int $id): string
    {
        try {
            $this->dewanParokiBidangService->moveUp($id);
        } catch (DomainException | RuntimeException $e) {
            return $this->listErrorResponse($e->getMessage());
        }

        return view('admin/dewan_paroki_bidang/partials/list', [
            'items'       => $this->dewanParokiBidangService->findAllOrdered(),
            'kodeOptions' => $this->dewanParokiBidangService->kodeOptions(),
        ]);
    }

    public function moveDown(int $id): string
    {
        try {
            $this->dewanParokiBidangService->moveDown($id);
        } catch (DomainException | RuntimeException $e) {
            return $this->listErrorResponse($e->getMessage());
        }

        return view('admin/dewan_paroki_bidang/partials/list', [
            'items'       => $this->dewanParokiBidangService->findAllOrdered(),
            'kodeOptions' => $this->dewanParokiBidangService->kodeOptions(),
        ]);
    }

    /**
     * @return array<string, array<int, string>|string>
     */
    private function formRules(bool $isCreate): array
    {
        $rules = [
            'nama_tampilan' => 'required|min_length[2]|max_length[255]',
            'deskripsi'     => 'permit_empty|max_length[5000]',
            'icon'          => 'required|max_length[100]',
            'urutan'        => 'permit_empty|is_natural',
        ];

        if ($isCreate) {
            $rules['kode'] = 'required|in_list[liturgi,diakonia,koinonia,kerygma]';
        }

        return $rules;
    }

    private function buildDtoFromRequest(int $urutan = 0): DewanParokiBidangDto
    {
        $deskripsi = trim((string) $this->request->getPost('deskripsi'));

        return new DewanParokiBidangDto(
            kode: (string) $this->request->getPost('kode'),
            namaTampilan: trim((string) $this->request->getPost('nama_tampilan')),
            deskripsi: $deskripsi !== '' ? $deskripsi : null,
            icon: trim((string) $this->request->getPost('icon')),
            urutan: $urutan > 0 ? $urutan : (int) ($this->request->getPost('urutan') ?? 0),
        );
    }

    private function validationErrorResponse(): string
    {
        return view('admin/dewan_paroki_bidang/partials/form', [
            'item'        => $this->requestFromOldInput(),
            'action'      => $this->resolveFormAction(),
            'kodeOptions' => $this->dewanParokiBidangService->kodeOptions(),
            'validation'  => $this->validator,
        ]);
    }

    private function formErrorResponse(string $message): string
    {
        return view('admin/dewan_paroki_bidang/partials/form_error', ['message' => $message]);
    }

    private function listErrorResponse(string $message): string
    {
        return view('admin/dewan_paroki_bidang/partials/list_error', ['message' => $message]);
    }

    private function resolveFormAction(): string
    {
        $id = (int) $this->request->getPost('id');

        return $id > 0
            ? site_url('admin/dewan-paroki/' . $id)
            : site_url('admin/dewan-paroki');
    }

    private function requestFromOldInput(): DewanParokiBidang
    {
        $item = new DewanParokiBidang();
        $item->id            = (int) ($this->request->getPost('id') ?? 0);
        $item->kode          = (string) $this->request->getPost('kode');
        $item->nama_tampilan = (string) $this->request->getPost('nama_tampilan');
        $item->deskripsi     = trim((string) $this->request->getPost('deskripsi')) ?: null;
        $item->icon          = (string) $this->request->getPost('icon');
        $item->urutan        = (int) ($this->request->getPost('urutan') ?? 0);

        return $item;
    }
}
