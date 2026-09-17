<?php

namespace App\Services\Pricing;

class HourEstimator
{
    public function calculateBaseHours(array $features): float
    {
        $total = 0.0;

        foreach ($features as $feature) {
            $total += $feature->getTotalHours();
        }

        return $total;
    }
}
