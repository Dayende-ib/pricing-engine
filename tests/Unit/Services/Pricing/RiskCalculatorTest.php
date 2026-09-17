<?php

namespace Tests\Unit\Services\Pricing;

use App\DTOs\Pricing\AdjustmentInput;
use App\Enums\AdjustmentType;
use App\Services\Pricing\RiskCalculator;
use PHPUnit\Framework\TestCase;

class RiskCalculatorTest extends TestCase
{
    private RiskCalculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new RiskCalculator;
    }

    public function test_calculate_adjustments_percentage(): void
    {
        $adjustments = [
            new AdjustmentInput('Urgency', 'Rush', AdjustmentType::Percentage, 20, ''),
            new AdjustmentInput('Complexity', 'Complex', AdjustmentType::Percentage, 15, ''),
        ];

        $total = $this->calculator->calculateAdjustmentsTotal($adjustments, 100000);

        $this->assertEquals(35000, $total); // 20% + 15% = 35% of 100000
    }

    public function test_calculate_adjustments_fixed(): void
    {
        $adjustments = [
            new AdjustmentInput('Support', 'Support', AdjustmentType::Fixed, 50000, ''),
            new AdjustmentInput('Training', 'Training', AdjustmentType::Fixed, 30000, ''),
        ];

        $total = $this->calculator->calculateAdjustmentsTotal($adjustments, 100000);

        $this->assertEquals(80000, $total);
    }

    public function test_calculate_adjustments_mixed(): void
    {
        $adjustments = [
            new AdjustmentInput('Urgency', 'Rush', AdjustmentType::Percentage, 10, ''),
            new AdjustmentInput('Support', 'Support', AdjustmentType::Fixed, 50000, ''),
        ];

        $total = $this->calculator->calculateAdjustmentsTotal($adjustments, 100000);

        $this->assertEquals(60000, $total); // 10% of 100000 + 50000
    }

    public function test_empty_adjustments(): void
    {
        $total = $this->calculator->calculateAdjustmentsTotal([], 100000);
        $this->assertEquals(0, $total);
    }
}
