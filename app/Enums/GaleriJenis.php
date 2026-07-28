<?php

declare(strict_types=1);

namespace App\Enums;

enum GaleriJenis: string
{
    case Foto  = 'foto';
    case Video = 'video';

    public function label(): string
    {
        return match ($this) {
            self::Foto  => 'Foto',
            self::Video => 'Video',
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
}
