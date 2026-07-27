<?php

declare(strict_types=1);

namespace App\Enums;

enum PublishStatus: string
{
    case Draft  = 'draft';
    case Terbit = 'terbit';

    public function label(): string
    {
        return match ($this) {
            self::Draft  => 'Draft',
            self::Terbit => 'Terbit',
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
