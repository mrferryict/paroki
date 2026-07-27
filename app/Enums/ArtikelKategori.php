<?php

declare(strict_types=1);

namespace App\Enums;

enum ArtikelKategori: string
{
    case ArtikelIman     = 'artikel_iman';
    case RenunganHarian  = 'renungan_harian';
    case OrangKudus      = 'orang_kudus';
    case MutiaraBiblika  = 'mutiara_biblika';

    public function label(): string
    {
        return match ($this) {
            self::ArtikelIman    => 'Artikel Iman',
            self::RenunganHarian => 'Renungan Harian',
            self::OrangKudus     => 'Orang Kudus',
            self::MutiaraBiblika => 'Mutiara Biblika',
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
