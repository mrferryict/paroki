<?php

declare(strict_types=1);

namespace App\Enums;

enum PendaftaranStatus: string
{
    case Baru     = 'baru';
    case Diproses = 'diproses';
    case Selesai  = 'selesai';
    case Ditolak  = 'ditolak';

    public function label(): string
    {
        return match ($this) {
            self::Baru     => 'Baru',
            self::Diproses => 'Diproses',
            self::Selesai  => 'Selesai',
            self::Ditolak  => 'Ditolak',
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
