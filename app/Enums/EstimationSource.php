<?php

namespace App\Enums;

enum EstimationSource: string
{
    case AI = 'ai';
    case Human = 'human';
    case Template = 'template';
    case Historical = 'historical';

    public function label(): string
    {
        return match ($this) {
            self::AI => 'IA',
            self::Human => 'Manuel',
            self::Template => 'Modèle',
            self::Historical => 'Historique',
        };
    }
}
