<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\DTOs\SakramenJenis\SakramenJenisDto;
use App\Entities\SakramenJenis;
use App\Enums\SakramenJenisKode;
use App\Services\SakramenJenisService;
use CodeIgniter\HTTP\ResponseInterface;
use DomainException;
use RuntimeException;

class SakramenJenisController extends BaseController
{
    private SakramenJenisService $sakramenJenisService;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger,
    ): void {
        parent::initController($request, $response, $logger);

        $this->sakramenJenisService = service('sakramenJenisService');
    }

    public function index(): string
    {
        $items = $this->sakramenJenisService->findAllOrdered();

        if ($this->isHtmxRequest()) {
            return view('admin/sakramen_jenis/partials/list', [
                'items'       => $items,
                'kodeOptions' => $this->sakramenJenisService->kodeOptions(),
                'grupOptions' => $this->sakramenJenisService->grupOptions(),
            ]);
        }

        return view('admin/sakramen_jenis/index', [
            'items'       => $items,
            'title'       => 'Layanan Paroki',
            'kodeOptions' => $this->sakramenJenisService->kodeOptions(),
            'grupOptions' => $this->sakramenJenisService->grupOptions(),
        ]);
    }

    public function new(): string
    {
        return view('admin/sakramen_jenis/partials/form', [
            'item'        => null,
            'action'      => site_url('admin/sakramen-jenis'),
            'kodeOptions' => $this->sakramenJenisService->kodeOptions(),
        ]);
    }

    public function create(): ResponseInterface|string
    {
        if (! $this->validate($this->formRules(isCreate: true))) {
            return $this->validationErrorResponse();
        }

        try {
            $this->sakramenJenisService->create($this->buildDtoFromRequest());

            session()->setFlashdata('success', 'Jenis sakramen/layanan berhasil ditambahkan.');

            return $this->htmxRedirect(site_url('admin/sakramen-jenis'));
        } catch (DomainException | RuntimeException $e) {
            return $this->formErrorResponse($e->getMessage());
        }
    }

    public function edit(int $id): string
    {
        try {
            $item = $this->sakramenJenisService->findById($id);
        } catch (DomainException) {
            return $this->formErrorResponse('Jenis sakramen/layanan tidak ditemukan.');
        }

        return view('admin/sakramen_jenis/partials/form', [
            'item'        => $item,
            'action'      => site_url('admin/sakramen-jenis/' . $id),
            'kodeOptions' => $this->sakramenJenisService->kodeOptions(),
        ]);
    }

    public function update(int $id): ResponseInterface|string
    {
        if (! $this->validate($this->formRules(isCreate: false))) {
            return $this->validationErrorResponse();
        }

        try {
            $existing = $this->sakramenJenisService->findById($id);
            $dto      = $this->buildDtoFromRequest(urutan: (int) $existing->urutan);

            $this->sakramenJenisService->update(id: $id, dto: $dto);

            session()->setFlashdata('success', 'Jenis sakramen/layanan berhasil diperbarui.');

            return $this->htmxRedirect(site_url('admin/sakramen-jenis'));
        } catch (DomainException | RuntimeException $e) {
            return $this->formErrorResponse($e->getMessage());
        }
    }

    public function delete(int $id): ResponseInterface|string
    {
        try {
            $this->sakramenJenisService->delete($id);

            session()->setFlashdata('success', 'Jenis sakramen/layanan berhasil dihapus.');

            return view('admin/sakramen_jenis/partials/list', [
                'items'       => $this->sakramenJenisService->findAllOrdered(),
                'kodeOptions' => $this->sakramenJenisService->kodeOptions(),
                'grupOptions' => $this->sakramenJenisService->grupOptions(),
            ]);
        } catch (DomainException | RuntimeException $e) {
            return $this->listErrorResponse($e->getMessage());
        }
    }

    public function moveUp(int $id): string
    {
        try {
            $this->sakramenJenisService->moveUp($id);
        } catch (DomainException | RuntimeException $e) {
            return $this->listErrorResponse($e->getMessage());
        }

        return view('admin/sakramen_jenis/partials/list', [
            'items'       => $this->sakramenJenisService->findAllOrdered(),
                'kodeOptions' => $this->sakramenJenisService->kodeOptions(),
                'grupOptions' => $this->sakramenJenisService->grupOptions(),
        ]);
    }

    public function moveDown(int $id): string
    {
        try {
            $this->sakramenJenisService->moveDown($id);
        } catch (DomainException | RuntimeException $e) {
            return $this->listErrorResponse($e->getMessage());
        }

        return view('admin/sakramen_jenis/partials/list', [
            'items'       => $this->sakramenJenisService->findAllOrdered(),
                'kodeOptions' => $this->sakramenJenisService->kodeOptions(),
                'grupOptions' => $this->sakramenJenisService->grupOptions(),
        ]);
    }

    /**
     * @return array<string, array<int, string>|string>
     */
    private function formRules(bool $isCreate): array
    {
        $rules = [
            'nama'      => 'required|min_length[2]|max_length[255]',
            'deskripsi' => 'permit_empty|max_length[5000]',
            'icon'      => 'required|max_length[100]',
            'urutan'    => 'permit_empty|is_natural',
            'is_active' => 'permit_empty|in_list[0,1]',
        ];

        if ($isCreate) {
            $rules['kode'] = 'required|in_list[' . implode(',', array_keys(SakramenJenisKode::options())) . ']';
        }

        return $rules;
    }

    private function buildDtoFromRequest(int $urutan = 0): SakramenJenisDto
    {
        $deskripsi = trim((string) $this->request->getPost('deskripsi'));

        return new SakramenJenisDto(
            kode: (string) $this->request->getPost('kode'),
            grup: $this->sakramenJenisService->resolveGrupForKode((string) $this->request->getPost('kode')),
            nama: trim((string) $this->request->getPost('nama')),
            deskripsi: $deskripsi !== '' ? $deskripsi : null,
            icon: trim((string) $this->request->getPost('icon')),
            urutan: $urutan > 0 ? $urutan : (int) ($this->request->getPost('urutan') ?? 0),
            isActive: $this->request->getPost('is_active') === '1',
        );
    }

    private function validationErrorResponse(): string
    {
        return view('admin/sakramen_jenis/partials/form', [
            'item'        => $this->requestFromOldInput(),
            'action'      => $this->resolveFormAction(),
            'kodeOptions' => $this->sakramenJenisService->kodeOptions(),
            'validation'  => $this->validator,
        ]);
    }

    private function formErrorResponse(string $message): string
    {
        return view('admin/sakramen_jenis/partials/form_error', ['message' => $message]);
    }

    private function listErrorResponse(string $message): string
    {
        return view('admin/sakramen_jenis/partials/list_error', ['message' => $message]);
    }

    private function resolveFormAction(): string
    {
        $id = (int) $this->request->getPost('id');

        return $id > 0
            ? site_url('admin/sakramen-jenis/' . $id)
            : site_url('admin/sakramen-jenis');
    }

    private function requestFromOldInput(): SakramenJenis
    {
        $item = new SakramenJenis();
        $item->id        = (int) ($this->request->getPost('id') ?? 0);
        $item->kode      = (string) $this->request->getPost('kode');
        $item->nama      = (string) $this->request->getPost('nama');
        $item->deskripsi = trim((string) $this->request->getPost('deskripsi')) ?: null;
        $item->icon      = (string) $this->request->getPost('icon');
        $item->urutan    = (int) ($this->request->getPost('urutan') ?? 0);
        $item->is_active = $this->request->getPost('is_active') === '1';

        return $item;
    }
}
