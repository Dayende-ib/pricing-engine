<?php

namespace App\Enums;

enum AdjustmentType: string
{
    case Percentage = 'percentage';
    case Fixed = 'fixed';

    public function label(): string
    {
        return match ($this) {
            self::Percentage => 'Pourcentage',
            self::Fixed => 'Montant fixe',
        };
    }
}
