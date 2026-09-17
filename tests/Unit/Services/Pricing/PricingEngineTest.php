<?php

namespace Tests\Unit\Services\Pricing;

use App\DTOs\Pricing\AdjustmentInput;
use App\DTOs\Pricing\ExternalCostInput;
use App\DTOs\Pricing\FeatureInput;
use App\DTOs\Pricing\PricingInput;
use App\Enums\AdjustmentType;
use App\Enums\ComplexityLevel;
use App\Enums\EstimationSource;
use App\Enums\ExternalCostFrequency;
use App\Enums\PricingTier;
use App\Enums\RiskLevel;
use App\Services\Pricing\HourEstimator;
use App\Services\Pricing\MarginCalculator;
use App\Services\Pricing\PriceRangeCalculator;
use App\Services\Pricing\PricingEngine;
use App\Services\Pricing\RiskCalculator;
use PHPUnit\Framework\TestCase;

class PricingEngineTest extends TestCase
{
    private PricingEngine $engine;

    private object $profile;

    protected function setUp(): void
    {
        $this->engine = new PricingEngine(
            new HourEstimator,
            new RiskCalculator,
            new MarginCalculator,
            new PriceRangeCalculator,
        );

        $this->profile = new class
        {
            public int $id = 1;

            public string $currency = 'XOF';

            public float $hourly_rate = 4000;

            public float $minimum_margin = 0.20;

            public float $target_margin = 0.35;

            public float $premium_margin = 0.50;

            public float $default_risk_reserve = 0.10;

            public int $default_revision_hours = 0;
        };
    }

    public function test_basic_calculation(): void
    {
        $input = new PricingInput(
            features: [
                new FeatureInput('Auth', 'Authentication', 1, 8, ComplexityLevel::Normal, 'high', EstimationSource::Human),
                new FeatureInput('Products', 'Product management', 1, 12, ComplexityLevel::Normal, 'high', EstimationSource::Human),
            ],
            pricingProfile: $this->profile,
            complexity: ComplexityLevel::Normal,
            riskLevel: RiskLevel::Low,
        );

        $result = $this->engine->calculate($input);

        $this->assertEquals(22.0, $result->totalHours); // 20 * 1.1 * 1.0
        $this->assertEquals(88000, $result->baseDevelopmentCost); // 22 * 4000
        $this->assertEquals(0, $result->externalCostsTotal);
        $this->assertEquals(0, $result->adjustmentsTotal);
        $this->assertEquals(8800, $result->riskReserve); // 10% of 88000
        $this->assertEquals(96800, $result->costBeforeMargin);

        // floor = 96800 / 0.8 = 121000 -> ceiling to 5000 = 125000
        // target = 96800 / 0.65 = 148923 -> ceiling to 5000 = 150000
        // premium = 96800 / 0.5 = 193600 -> ceiling to 5000 = 195000
        $this->assertEquals(125000, $result->floorPrice);
        $this->assertEquals(150000, $result->targetPrice);
        $this->assertEquals(195000, $result->premiumPrice);
    }

    public function test_with_external_costs(): void
    {
        $input = new PricingInput(
            features: [
                new FeatureInput('Auth', 'Authentication', 1, 10, ComplexityLevel::Normal, 'high', EstimationSource::Human),
            ],
            pricingProfile: $this->profile,
            externalCosts: [
                new ExternalCostInput('Hosting', 'Monthly hosting', 10000, ExternalCostFrequency::Monthly, 1),
                new ExternalCostInput('Domain', 'Yearly domain', 15000, ExternalCostFrequency::Yearly, 1),
            ],
            projectDurationMonths: 12,
            complexity: ComplexityLevel::Normal,
            riskLevel: RiskLevel::Low,
        );

        $result = $this->engine->calculate($input);

        // 10h * 1.1 = 11h * 4000 = 44000
        $this->assertEquals(11.0, $result->totalHours);
        $this->assertEquals(44000, $result->baseDevelopmentCost);

        // External: 10000 * 12 + 15000 * 1 = 135000
        $this->assertEquals(135000, $result->externalCostsTotal);

        // costBeforeMargin = 44000 + 135000 + 4400 (risk) = 183400
        $this->assertEquals(183400, $result->costBeforeMargin);
    }

    public function test_with_percentage_adjustments(): void
    {
        $input = new PricingInput(
            features: [
                new FeatureInput('Auth', 'Authentication', 1, 10, ComplexityLevel::Normal, 'high', EstimationSource::Human),
            ],
            pricingProfile: $this->profile,
            adjustments: [
                new AdjustmentInput('Urgency', 'Rush job', AdjustmentType::Percentage, 20, 'Client needs it fast'),
            ],
            complexity: ComplexityLevel::Normal,
            riskLevel: RiskLevel::Low,
        );

        $result = $this->engine->calculate($input);

        // 10h * 1.1 = 11h * 4000 = 44000
        // Adjustment: 20% of 44000 = 8800
        $this->assertEquals(8800, $result->adjustmentsTotal);
        $this->assertEquals(44000 + 8800 + 4400, $result->costBeforeMargin); // 57200
    }

    public function test_with_fixed_adjustments(): void
    {
        $input = new PricingInput(
            features: [
                new FeatureInput('Auth', 'Authentication', 1, 10, ComplexityLevel::Normal, 'high', EstimationSource::Human),
            ],
            pricingProfile: $this->profile,
            adjustments: [
                new AdjustmentInput('Support', 'Extended support', AdjustmentType::Fixed, 100000, '1 year support'),
            ],
            complexity: ComplexityLevel::Normal,
            riskLevel: RiskLevel::Low,
        );

        $result = $this->engine->calculate($input);

        $this->assertEquals(100000, $result->adjustmentsTotal);
    }

    public function test_complexity_factors(): void
    {
        $baseFeatures = [
            new FeatureInput('Feature', 'Test', 1, 10, ComplexityLevel::Normal, 'normal', EstimationSource::Human),
        ];

        $low = $this->engine->calculate(new PricingInput(
            features: $baseFeatures,
            pricingProfile: $this->profile,
            complexity: ComplexityLevel::Low,
            riskLevel: RiskLevel::Low,
        ));

        $high = $this->engine->calculate(new PricingInput(
            features: $baseFeatures,
            pricingProfile: $this->profile,
            complexity: ComplexityLevel::High,
            riskLevel: RiskLevel::Low,
        ));

        $veryHigh = $this->engine->calculate(new PricingInput(
            features: $baseFeatures,
            pricingProfile: $this->profile,
            complexity: ComplexityLevel::VeryHigh,
            riskLevel: RiskLevel::Low,
        ));

        $this->assertEquals(10.0, $low->totalHours); // 10 * 1.0 * 1.0
        $this->assertEquals(12.5, $high->totalHours); // 10 * 1.25 * 1.0
        $this->assertEquals(14.0, $veryHigh->totalHours); // 10 * 1.4 * 1.0
    }

    public function test_risk_factors(): void
    {
        $baseFeatures = [
            new FeatureInput('Feature', 'Test', 1, 10, ComplexityLevel::Normal, 'normal', EstimationSource::Human),
        ];

        $low = $this->engine->calculate(new PricingInput(
            features: $baseFeatures,
            pricingProfile: $this->profile,
            complexity: ComplexityLevel::Normal,
            riskLevel: RiskLevel::Low,
        ));

        $medium = $this->engine->calculate(new PricingInput(
            features: $baseFeatures,
            pricingProfile: $this->profile,
            complexity: ComplexityLevel::Normal,
            riskLevel: RiskLevel::Medium,
        ));

        $high = $this->engine->calculate(new PricingInput(
            features: $baseFeatures,
            pricingProfile: $this->profile,
            complexity: ComplexityLevel::Normal,
            riskLevel: RiskLevel::High,
        ));

        $this->assertEquals(11.0, $low->totalHours); // 10 * 1.1 * 1.0
        $this->assertEqualsWithDelta(12.1, $medium->totalHours, 0.001); // 10 * 1.1 * 1.1
        $this->assertEqualsWithDelta(13.2, $high->totalHours, 0.001); // 10 * 1.1 * 1.2
    }

    public function test_zero_features(): void
    {
        $input = new PricingInput(
            features: [],
            pricingProfile: $this->profile,
        );

        $result = $this->engine->calculate($input);

        $this->assertEquals(0, $result->totalHours);
        $this->assertEquals(0, $result->baseDevelopmentCost);
        $this->assertEquals(0, $result->riskReserve);
        $this->assertEquals(0, $result->costBeforeMargin);
        $this->assertEquals(0, $result->floorPrice);
    }

    public function test_high_margin_edge_case(): void
    {
        $highMarginProfile = new class
        {
            public string $currency = 'XOF';

            public float $hourly_rate = 4000;

            public float $minimum_margin = 0.10;

            public float $target_margin = 0.90; // 90% margin

            public float $premium_margin = 0.95; // 95% margin

            public float $default_risk_reserve = 0.10;
        };

        $input = new PricingInput(
            features: [
                new FeatureInput('Feature', 'Test', 1, 10, ComplexityLevel::Normal, 'normal', EstimationSource::Human),
            ],
            pricingProfile: $highMarginProfile,
        );

        $result = $this->engine->calculate($input);

        // With 90% margin: cost / 0.1 = 10x cost
        // With 95% margin: cost / 0.05 = 20x cost
        // targetPrice should be much higher than costBeforeMargin
        $this->assertGreaterThan($result->costBeforeMargin * 5, $result->targetPrice);
    }

    public function test_get_price_by_tier(): void
    {
        $input = new PricingInput(
            features: [
                new FeatureInput('Feature', 'Test', 1, 10, ComplexityLevel::Normal, 'normal', EstimationSource::Human),
            ],
            pricingProfile: $this->profile,
        );

        $result = $this->engine->calculate($input);

        $this->assertEquals($result->floorPrice, $result->getPrice(PricingTier::Floor));
        $this->assertEquals($result->targetPrice, $result->getPrice(PricingTier::Target));
        $this->assertEquals($result->premiumPrice, $result->getPrice(PricingTier::Premium));
    }

    public function test_get_margin_by_tier(): void
    {
        $input = new PricingInput(
            features: [
                new FeatureInput('Feature', 'Test', 1, 10, ComplexityLevel::Normal, 'normal', EstimationSource::Human),
            ],
            pricingProfile: $this->profile,
        );

        $result = $this->engine->calculate($input);

        // Use raw prices for accurate margin calculation
        $floorMargin = (($result->rawFloorPrice - $result->costBeforeMargin) / $result->rawFloorPrice) * 100;
        $targetMargin = (($result->rawTargetPrice - $result->costBeforeMargin) / $result->rawTargetPrice) * 100;
        $premiumMargin = (($result->rawPremiumPrice - $result->costBeforeMargin) / $result->rawPremiumPrice) * 100;

        $this->assertEqualsWithDelta(20.0, $floorMargin, 0.1);
        $this->assertEqualsWithDelta(35.0, $targetMargin, 0.1);
        $this->assertEqualsWithDelta(50.0, $premiumMargin, 0.1);
    }

    public function test_breakdown_is_populated(): void
    {
        $input = new PricingInput(
            features: [
                new FeatureInput('Auth', 'Authentication', 1, 8, ComplexityLevel::Normal, 'high', EstimationSource::Human),
            ],
            pricingProfile: $this->profile,
        );

        $result = $this->engine->calculate($input);

        $this->assertNotEmpty($result->breakdown);
        $this->assertGreaterThan(10, count($result->breakdown));

        $labels = array_column($result->breakdown, 'label');
        $this->assertContains('Heures de base', $labels);
        $this->assertContains('Facteur complexité', $labels);
        $this->assertContains('Coût développement', $labels);
        $this->assertContains('Coût avant marge', $labels);
        $this->assertContains('Prix cible (arrondi)', $labels);
    }

    public function test_currency_rounding_xof(): void
    {
        $input = new PricingInput(
            features: [
                new FeatureInput('Feature', 'Test', 1, 10, ComplexityLevel::Normal, 'normal', EstimationSource::Human),
            ],
            pricingProfile: $this->profile,
            currency: 'XOF',
        );

        $result = $this->engine->calculate($input);

        // XOF should round to nearest 1000 for prices > 10000
        $this->assertEquals(0, $result->targetPrice % 1000);
    }

    public function test_quantity_multiplier(): void
    {
        $input = new PricingInput(
            features: [
                new FeatureInput('Auth', 'Authentication', 3, 8, ComplexityLevel::Normal, 'high', EstimationSource::Human),
            ],
            pricingProfile: $this->profile,
        );

        $result = $this->engine->calculate($input);

        // 8h * 3 = 24h base * 1.1 = 26.4h
        $this->assertEqualsWithDelta(26.4, $result->totalHours, 0.001);
    }
}
