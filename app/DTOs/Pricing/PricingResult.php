<?php

namespace App\DTOs\Pricing;

use App\Enums\PricingTier;

readonly class PricingResult
{
    /**
     * @param  array<PriceBreakdown>  $breakdown
     */
    public function __construct(
        public float $totalHours,
        public float $baseDevelopmentCost,
        public float $externalCostsTotal,
        public float $adjustmentsTotal,
        public float $riskReserve,
        public float $costBeforeMargin,
        public float $floorPrice,
        public float $targetPrice,
        public float $premiumPrice,
        public string $currency,
        public array $breakdown = [],
        public ?float $rawFloorPrice = null,
        public ?float $rawTargetPrice = null,
        public ?float $rawPremiumPrice = null,
    ) {}

    public function getPrice(PricingTier $tier): float
    {
        return match ($tier) {
            PricingTier::Floor => $this->floorPrice,
            PricingTier::Target => $this->targetPrice,
            PricingTier::Premium => $this->premiumPrice,
        };
    }

    public function getRawPrice(PricingTier $tier): float
    {
        return match ($tier) {
            PricingTier::Floor => $this->rawFloorPrice ?? $this->floorPrice,
            PricingTier::Target => $this->rawTargetPrice ?? $this->targetPrice,
            PricingTier::Premium => $this->rawPremiumPrice ?? $this->premiumPrice,
        };
    }

    public function getMargin(PricingTier $tier): float
    {
        if ($this->costBeforeMargin <= 0) {
            return 0;
        }

        $price = $this->getPrice($tier);

        return (($price - $this->costBeforeMargin) / $price) * 100;
    }
}
