<?php

namespace App\Enums;

enum ComplexityLevel: string
{
    case Low = 'low';
    case Normal = 'normal';
    case High = 'high';
    case VeryHigh = 'very_high';

    public function label(): string
    {
        return match ($this) {
            self::Low => 'Faible',
            self::Normal => 'Normale',
            self::High => 'Élevée',
            self::VeryHigh => 'Très élevée',
        };
    }

    public function factor(): float
    {
        return match ($this) {
            self::Low => 1.00,
            self::Normal => 1.10,
            self::High => 1.25,
            self::VeryHigh => 1.40,
        };
    }
}
