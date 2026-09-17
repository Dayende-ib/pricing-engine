<?php

namespace App\Services\Pricing;

class PriceRangeCalculator
{
    public function roundPrice(float $price, string $currency = 'XOF'): float
    {
        return match ($currency) {
            'XOF', 'EUR', 'USD' => $this->roundCommercialUp($price),
            default => $price,
        };
    }

    private function roundCommercialUp(float $price): float
    {
        if ($price <= 0) {
            return 0;
        }

        $increment = $this->getIncrement($price);

        // Round UP to the next increment (ceiling) for clean commercial pricing
        return ceil($price / $increment) * $increment;
    }

    private function getIncrement(float $price): int
    {
        return match (true) {
            $price >= 1000000 => 50000,
            $price >= 100000 => 5000,
            $price >= 10000 => 1000,
            $price >= 1000 => 100,
            $price >= 100 => 50,
            default => 10,
        };
    }
}
