<?php

namespace App\Services\Pricing;

use App\DTOs\Pricing\PriceBreakdown;
use App\DTOs\Pricing\PricingInput;
use App\DTOs\Pricing\PricingResult;

class PricingEngine
{
    public function __construct(
        private readonly HourEstimator $hourEstimator,
        private readonly RiskCalculator $riskCalculator,
        private readonly MarginCalculator $marginCalculator,
        private readonly PriceRangeCalculator $priceRangeCalculator,
    ) {}

    public function calculate(PricingInput $input): PricingResult
    {
        $baseHours = $this->hourEstimator->calculateBaseHours($input->features);

        $complexityFactor = $input->complexity->factor();
        $riskFactor = $input->riskLevel->factor();

        $adjustedHours = $baseHours * $complexityFactor * $riskFactor;

        $hourlyRate = (float) $input->pricingProfile->hourly_rate;
        $developmentCost = $adjustedHours * $hourlyRate;

        $externalCostsTotal = $this->calculateExternalCosts($input->externalCosts, $input->projectDurationMonths);

        $adjustmentsTotal = $this->riskCalculator->calculateAdjustmentsTotal($input->adjustments, $developmentCost);

        $riskReserve = $developmentCost * (float) $input->pricingProfile->default_risk_reserve;

        $costBeforeMargin = $developmentCost + $externalCostsTotal + $adjustmentsTotal + $riskReserve;

        $prices = $this->marginCalculator->calculatePrices($costBeforeMargin, $input->pricingProfile);

        $rawFloorPrice = $prices['floor'];
        $rawTargetPrice = $prices['target'];
        $rawPremiumPrice = $prices['premium'];

        $floorPrice = $this->priceRangeCalculator->roundPrice($rawFloorPrice, $input->currency);
        $targetPrice = $this->priceRangeCalculator->roundPrice($rawTargetPrice, $input->currency);
        $premiumPrice = $this->priceRangeCalculator->roundPrice($rawPremiumPrice, $input->currency);

        $breakdown = $this->buildBreakdown(
            $input,
            $baseHours,
            $adjustedHours,
            $hourlyRate,
            $developmentCost,
            $externalCostsTotal,
            $adjustmentsTotal,
            $riskReserve,
            $costBeforeMargin,
            $rawFloorPrice,
            $rawTargetPrice,
            $rawPremiumPrice,
            $floorPrice,
            $targetPrice,
            $premiumPrice,
        );

        return new PricingResult(
            totalHours: $adjustedHours,
            baseDevelopmentCost: $developmentCost,
            externalCostsTotal: $externalCostsTotal,
            adjustmentsTotal: $adjustmentsTotal,
            riskReserve: $riskReserve,
            costBeforeMargin: $costBeforeMargin,
            floorPrice: $floorPrice,
            targetPrice: $targetPrice,
            premiumPrice: $premiumPrice,
            currency: $input->currency,
            breakdown: $breakdown,
            rawFloorPrice: $rawFloorPrice,
            rawTargetPrice: $rawTargetPrice,
            rawPremiumPrice: $rawPremiumPrice,
        );
    }

    private function calculateExternalCosts(array $externalCosts, int $months): float
    {
        $total = 0.0;

        foreach ($externalCosts as $cost) {
            $total += $cost->getTotalCost($months);
        }

        return $total;
    }

    /**
     * @return array<PriceBreakdown>
     */
    private function buildBreakdown(
        PricingInput $input,
        float $baseHours,
        float $adjustedHours,
        float $hourlyRate,
        float $developmentCost,
        float $externalCostsTotal,
        float $adjustmentsTotal,
        float $riskReserve,
        float $costBeforeMargin,
        float $rawFloorPrice,
        float $rawTargetPrice,
        float $rawPremiumPrice,
        float $floorPrice,
        float $targetPrice,
        float $premiumPrice,
    ): array {
        $breakdown = [];

        $breakdown[] = new PriceBreakdown('Heures de base', $baseHours, 'Somme des heures estimées × quantités');
        $breakdown[] = new PriceBreakdown('Facteur complexité', $input->complexity->factor(), $input->complexity->label());
        $breakdown[] = new PriceBreakdown('Facteur risque', $input->riskLevel->factor(), $input->riskLevel->label());
        $breakdown[] = new PriceBreakdown('Heures ajustées', $adjustedHours, "$baseHours × {$input->complexity->factor()} × {$input->riskLevel->factor()}");
        $breakdown[] = new PriceBreakdown('Taux horaire', $hourlyRate, $input->currency);
        $breakdown[] = new PriceBreakdown('Coût développement', $developmentCost, "$adjustedHours h × $hourlyRate {$input->currency}/h");

        if ($externalCostsTotal > 0) {
            $breakdown[] = new PriceBreakdown('Coûts externes', $externalCostsTotal, "Sur {$input->projectDurationMonths} mois");
        }

        if ($adjustmentsTotal > 0) {
            $breakdown[] = new PriceBreakdown('Ajustements', $adjustmentsTotal, count($input->adjustments).' ajustement(s)');
        }

        if ($riskReserve > 0) {
            $reservePercent = (float) $input->pricingProfile->default_risk_reserve * 100;
            $breakdown[] = new PriceBreakdown('Réserve risque', $riskReserve, "{$reservePercent}% du coût développement");
        }

        $breakdown[] = new PriceBreakdown('Coût avant marge', $costBeforeMargin, 'Somme des coûts ci-dessus');

        $minMargin = (float) $input->pricingProfile->minimum_margin * 100;
        $targetMargin = (float) $input->pricingProfile->target_margin * 100;
        $premiumMargin = (float) $input->pricingProfile->premium_margin * 100;

        $breakdown[] = new PriceBreakdown('Prix plancher (brut)', $rawFloorPrice, "Marge {$minMargin}%");
        $breakdown[] = new PriceBreakdown('Prix cible (brut)', $rawTargetPrice, "Marge {$targetMargin}%");
        $breakdown[] = new PriceBreakdown('Prix premium (brut)', $rawPremiumPrice, "Marge {$premiumMargin}%");

        $breakdown[] = new PriceBreakdown('Prix plancher (arrondi)', $floorPrice, 'Arrondi commercial');
        $breakdown[] = new PriceBreakdown('Prix cible (arrondi)', $targetPrice, 'Arrondi commercial');
        $breakdown[] = new PriceBreakdown('Prix premium (arrondi)', $premiumPrice, 'Arrondi commercial');

        return $breakdown;
    }
}
