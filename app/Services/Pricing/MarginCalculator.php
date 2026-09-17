<?php

namespace App\Services\Pricing;

class MarginCalculator
{
    /**
     * @param  object  $profile  Must have minimum_margin, target_margin, premium_margin properties
     */
    public function calculatePrices(float $costBeforeMargin, object $profile): array
    {
        return [
            'floor' => $this->applyMargin($costBeforeMargin, (float) $profile->minimum_margin),
            'target' => $this->applyMargin($costBeforeMargin, (float) $profile->target_margin),
            'premium' => $this->applyMargin($costBeforeMargin, (float) $profile->premium_margin),
        ];
    }

    private function applyMargin(float $cost, float $margin): float
    {
        if ($margin >= 1.0) {
            return $cost;
        }

        return $cost / (1 - $margin);
    }
}
