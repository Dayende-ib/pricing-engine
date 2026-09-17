<?php

namespace App\Enums;

enum ExternalCostFrequency: string
{
    case OneTime = 'one_time';
    case Monthly = 'monthly';
    case Yearly = 'yearly';

    public function label(): string
    {
        return match ($this) {
            self::OneTime => 'Ponctuel',
            self::Monthly => 'Mensuel',
            self::Yearly => 'Annuel',
        };
    }
}
