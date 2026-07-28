<?php

declare(strict_types=1);

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Paroki extends BaseConfig
{
    /** Nomor WhatsApp internasional tanpa + (mis. 6281234567890). */
    public string $whatsappNumber = '';

    public function __construct()
    {
        parent::__construct();

        $this->whatsappNumber = (string) env('paroki.whatsapp', '6281234567890');
    }

    public function whatsappUrl(): string
    {
        $number = preg_replace('/\D+/', '', $this->whatsappNumber) ?? '';

        return $number !== '' ? 'https://wa.me/' . $number : '#';
    }
}
