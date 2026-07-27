<?php

declare(strict_types=1);

namespace App\Enums;

enum BeritaKategori: string
{
    case Pengumuman       = 'pengumuman';
    case KegiatanParoki   = 'kegiatan_paroki';
    case PelayananSosial  = 'pelayanan_sosial';
    case KegiatanWilayah  = 'kegiatan_wilayah';
    case Liturgi          = 'liturgi';

    public function label(): string
    {
        return match ($this) {
            self::Pengumuman      => 'Pengumuman',
            self::KegiatanParoki  => 'Kegiatan Paroki',
            self::PelayananSosial => 'Pelayanan Sosial',
            self::KegiatanWilayah => 'Kegiatan Wilayah',
            self::Liturgi         => 'Liturgi',
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
