<?php

namespace App\Enums;

enum RiskLevel: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';

    public function label(): string
    {
        return match ($this) {
            self::Low => 'Faible',
            self::Medium => 'Moyen',
            self::High => 'Élevé',
        };
    }

    public function factor(): float
    {
        return match ($this) {
            self::Low => 1.00,
            self::Medium => 1.10,
            self::High => 1.20,
        };
    }
}
