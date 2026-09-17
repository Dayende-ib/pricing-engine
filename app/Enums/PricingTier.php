<?php

namespace App\Enums;

enum PricingTier: string
{
    case Floor = 'floor';
    case Target = 'target';
    case Premium = 'premium';

    public function label(): string
    {
        return match ($this) {
            self::Floor => 'Plancher',
            self::Target => 'Cible',
            self::Premium => 'Premium',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Floor => 'Prix minimum pour couvrir les coûts',
            self::Target => 'Prix recommandé avec marge standard',
            self::Premium => 'Prix avec marge confortable',
        };
    }
}
