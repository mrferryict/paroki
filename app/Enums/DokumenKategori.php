<?php

declare(strict_types=1);

namespace App\Enums;

enum DokumenKategori: string
{
    case Formulir     = 'formulir';
    case WartaParoki  = 'warta_paroki';
    case Majalah      = 'majalah';
    case Dokumen      = 'dokumen';

    public function label(): string
    {
        return match ($this) {
            self::Formulir    => 'Formulir',
            self::WartaParoki => 'Warta Paroki',
            self::Majalah     => 'Majalah',
            self::Dokumen     => 'Dokumen',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }

    public static function tryFromString(?string $value): ?self
    {
        if ($value === null || $value === '') {
            return null;
        }

        return self::tryFrom($value);
    }
}
