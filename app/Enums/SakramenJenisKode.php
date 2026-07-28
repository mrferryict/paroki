<?php

declare(strict_types=1);

namespace App\Enums;

enum SakramenJenisKode: string
{
    case Baptis                 = 'baptis';
    case KomuniPertama          = 'komuni_pertama';
    case Krisma                 = 'krisma';
    case Tobat                  = 'tobat';
    case Perkawinan             = 'perkawinan';
    case PengurapanOrangSakit   = 'pengurapan_orang_sakit';
    case Imamat                 = 'imamat';
    case KonsultasiPsikologi    = 'konsultasi_psikologi';
    case KonsultasiHukum        = 'konsultasi_hukum';
    case Administrasi           = 'administrasi';
    case Misdinar               = 'misdinar';
    case Pemazmur               = 'pemazmur';
    case Prodiakon              = 'prodiakon';
    case Organis                = 'organis';

    public function label(): string
    {
        return match ($this) {
            self::Baptis               => 'Sakramen Baptis',
            self::KomuniPertama        => 'Komuni Pertama',
            self::Krisma               => 'Sakramen Krisma',
            self::Tobat                => 'Sakramen Tobat',
            self::Perkawinan           => 'Sakramen Perkawinan',
            self::PengurapanOrangSakit => 'Pengurapan Orang Sakit',
            self::Imamat               => 'Sakramen Imamat',
            self::KonsultasiPsikologi  => 'Konsultasi Psikologi',
            self::KonsultasiHukum      => 'Konsultasi Hukum',
            self::Administrasi         => 'Administrasi (Sekretariat)',
            self::Misdinar             => 'Misdinar',
            self::Pemazmur             => 'Pemazmur',
            self::Prodiakon            => 'Prodiakon',
            self::Organis              => 'Organis',
        };
    }

    public function grup(): LayananGrup
    {
        return match ($this) {
            self::Baptis,
            self::KomuniPertama,
            self::Krisma,
            self::Tobat,
            self::Perkawinan,
            self::PengurapanOrangSakit,
            self::Imamat               => LayananGrup::Sakramen,
            self::KonsultasiPsikologi,
            self::KonsultasiHukum      => LayananGrup::Konsultasi,
            self::Administrasi         => LayananGrup::Administrasi,
            self::Misdinar,
            self::Pemazmur,
            self::Prodiakon,
            self::Organis              => LayananGrup::Petugas,
        };
    }

    public function defaultIcon(): string
    {
        return match ($this) {
            self::Baptis               => 'baptis',
            self::KomuniPertama        => 'komuni-pertama',
            self::Krisma               => 'krisma',
            self::Tobat                => 'tobat',
            self::Perkawinan           => 'perkawinan',
            self::PengurapanOrangSakit => 'pengurapan',
            self::Imamat               => 'imamat',
            self::KonsultasiPsikologi  => 'konsultasi-psikologi',
            self::KonsultasiHukum     => 'konsultasi-hukum',
            self::Administrasi         => 'administrasi',
            self::Misdinar             => 'misdinar',
            self::Pemazmur             => 'pemazmur',
            self::Prodiakon            => 'prodiakon',
            self::Organis              => 'organis',
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

    /**
     * @return array<string, string>
     */
    public static function optionsForGrup(LayananGrup $grup): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            if ($case->grup() === $grup) {
                $options[$case->value] = $case->label();
            }
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
