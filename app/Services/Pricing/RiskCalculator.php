<?php

namespace App\Services\Pricing;

use App\Enums\AdjustmentType;
use App\Enums\ComplexityLevel;
use App\Enums\RiskLevel;

class RiskCalculator
{
    public function calculate(
        float $baseHours,
        float $hourlyRate,
        ComplexityLevel $complexity,
        RiskLevel $riskLevel,
        array $adjustments = [],
        float $externalCosts = 0.0,
    ): float {
        $complexityFactor = $complexity->factor();
        $riskFactor = $riskLevel->factor();

        $adjustedHours = $baseHours * $complexityFactor * $riskFactor;

        $developmentCost = $adjustedHours * $hourlyRate;

        $adjustmentAmount = $this->calculateAdjustments($adjustments, $developmentCost);

        return $developmentCost + $adjustmentAmount + $externalCosts;
    }

    private function calculateAdjustments(array $adjustments, float $baseCost): float
    {
        $total = 0.0;

        foreach ($adjustments as $adjustment) {
            if ($adjustment->type === AdjustmentType::Percentage) {
                $total += $baseCost * ($adjustment->value / 100);
            } else {
                $total += $adjustment->value;
            }
        }

        return $total;
    }

    public function calculateAdjustmentsTotal(array $adjustments, float $baseCost): float
    {
        return $this->calculateAdjustments($adjustments, $baseCost);
    }
}
