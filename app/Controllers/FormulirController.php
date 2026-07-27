<?php

declare(strict_types=1);

namespace App\Controllers;

use App\DTOs\Pendaftaran\PendaftaranDto;
use App\Services\PendaftaranService;
use InvalidArgumentException;
use RuntimeException;

class FormulirController extends BaseController
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

    public function submit(): string
    {
        if (! $this->validate($this->formRules())) {
            return view('partials/formulir/error', [
                'errors' => $this->validator->getErrors(),
            ]);
        }

        try {
            $this->pendaftaranService->submit($this->buildDtoFromRequest());

            return view('partials/formulir/success');
        } catch (InvalidArgumentException | RuntimeException $e) {
            return view('partials/formulir/error', [
                'errors' => ['form' => $e->getMessage()],
            ]);
        }
    }

    /**
     * @return array<string, array<int, string>|string>
     */
    private function formRules(): array
    {
        return [
            'nama_lengkap'      => 'required|min_length[2]|max_length[255]',
            'whatsapp'          => 'required|min_length[8]|max_length[20]',
            'sakramen_jenis_id' => 'permit_empty|is_natural_no_zero',
            'pesan'             => 'permit_empty|max_length[2000]',
        ];
    }

    private function buildDtoFromRequest(): PendaftaranDto
    {
        $sakramenId = (int) ($this->request->getPost('sakramen_jenis_id') ?? 0);
        $pesan      = trim((string) $this->request->getPost('pesan'));

        return new PendaftaranDto(
            namaLengkap: trim((string) $this->request->getPost('nama_lengkap')),
            whatsapp: trim((string) $this->request->getPost('whatsapp')),
            sakramenJenisId: $sakramenId > 0 ? $sakramenId : null,
            pesan: $pesan !== '' ? $pesan : null,
        );
    }
}
