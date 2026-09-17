<?php

namespace App\DTOs\Pricing;

use App\Enums\ComplexityLevel;
use App\Enums\RiskLevel;
use App\Models\ExternalCost;
use App\Models\Feature;
use App\Models\PricingAdjustment;
use App\Models\Project;

/**
 * @property-read float $hourly_rate
 * @property-read float $minimum_margin
 * @property-read float $target_margin
 * @property-read float $premium_margin
 * @property-read float $default_risk_reserve
 * @property-read string $currency
 */
class PricingInput
{
    /**
     * @param  array<FeatureInput>  $features
     * @param  array<ExternalCostInput>  $externalCosts
     * @param  array<AdjustmentInput>  $adjustments
     */
    public function __construct(
        public array $features,
        public object $pricingProfile,
        public array $externalCosts = [],
        public array $adjustments = [],
        public ComplexityLevel $complexity = ComplexityLevel::Normal,
        public RiskLevel $riskLevel = RiskLevel::Low,
        public int $projectDurationMonths = 1,
        public ?string $currency = null,
    ) {
        if ($this->currency === null) {
            $this->currency = $this->pricingProfile->currency ?? 'XOF';
        }
    }

    public static function fromProject(Project $project): self
    {
        $featureInputs = [];
        /** @var Feature $feature */
        foreach ($project->features as $feature) {
            $featureInputs[] = FeatureInput::fromFeature($feature);
        }

        $externalCostInputs = [];
        /** @var ExternalCost $externalCost */
        foreach ($project->externalCosts as $externalCost) {
            $externalCostInputs[] = ExternalCostInput::fromExternalCost($externalCost);
        }

        $adjustmentInputs = [];
        /** @var PricingAdjustment $adjustment */
        foreach ($project->pricingAdjustments as $adjustment) {
            $adjustmentInputs[] = AdjustmentInput::fromAdjustment($adjustment);
        }

        return new self(
            features: $featureInputs,
            pricingProfile: $project->pricingProfile,
            externalCosts: $externalCostInputs,
            adjustments: $adjustmentInputs,
            complexity: $project->complexity instanceof ComplexityLevel
                ? $project->complexity
                : ComplexityLevel::tryFrom((string) $project->complexity) ?? ComplexityLevel::Normal,
            riskLevel: $project->risk_level instanceof RiskLevel
                ? $project->risk_level
                : RiskLevel::tryFrom((string) $project->risk_level) ?? RiskLevel::Low,
            projectDurationMonths: $project->deadline ? max(1, now()->diffInMonths($project->deadline)) : 1,
            currency: $project->currency,
        );
    }
}
