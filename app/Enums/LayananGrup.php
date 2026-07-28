<?php

declare(strict_types=1);

namespace App\Enums;

enum LayananGrup: string
{
    case Sakramen      = 'sakramen';
    case Konsultasi    = 'konsultasi';
    case Administrasi  = 'administrasi';
    case Petugas       = 'petugas';

    public function label(): string
    {
        return match ($this) {
            self::Sakramen     => 'Sakramen',
            self::Konsultasi   => 'Konsultasi',
            self::Administrasi => 'Administrasi',
            self::Petugas      => 'Petugas',
        };
    }

    public function sectionLabel(): string
    {
        return match ($this) {
            self::Administrasi => 'Administrasi (Sekretariat)',
            default            => $this->label(),
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
