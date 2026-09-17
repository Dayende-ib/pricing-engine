<?php

namespace Tests\Unit\Services\Pricing;

use App\Services\Pricing\PriceRangeCalculator;
use PHPUnit\Framework\TestCase;

class PriceRangeCalculatorTest extends TestCase
{
    private PriceRangeCalculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new PriceRangeCalculator;
    }

    public function test_round_xof_large(): void
    {
        // >= 100000: increment 5000, ceiling
        $this->assertEquals(615000, $this->calculator->roundPrice(612438, 'XOF'));
        $this->assertEquals(620000, $this->calculator->roundPrice(618000, 'XOF'));
        $this->assertEquals(1050000, $this->calculator->roundPrice(1048000, 'XOF'));
    }

    public function test_round_xof_medium(): void
    {
        // >= 100000: increment 5000, ceiling
        $this->assertEquals(155000, $this->calculator->roundPrice(153438, 'XOF'));
        $this->assertEquals(150000, $this->calculator->roundPrice(148000, 'XOF'));
    }

    public function test_round_xof_small(): void
    {
        // >= 1000: increment 100, ceiling
        $this->assertEquals(5500, $this->calculator->roundPrice(5438, 'XOF'));
        // 4800/100 = 48 exactly, ceiling = 48
        $this->assertEquals(4800, $this->calculator->roundPrice(4800, 'XOF'));
    }

    public function test_round_xof_very_small(): void
    {
        // >= 100: increment 50, ceiling
        $this->assertEquals(550, $this->calculator->roundPrice(538, 'XOF'));
        // 480/50 = 9.6, ceiling = 10
        $this->assertEquals(500, $this->calculator->roundPrice(480, 'XOF'));
    }

    public function test_round_xof_tiny(): void
    {
        // < 100: increment 10, ceiling
        $this->assertEquals(40, $this->calculator->roundPrice(38, 'XOF'));
        // 80/10 = 8 exactly, ceiling = 8
        $this->assertEquals(80, $this->calculator->roundPrice(80, 'XOF'));
    }

    public function test_round_eur(): void
    {
        // >= 1000: increment 100, ceiling
        $this->assertEquals(6200, $this->calculator->roundPrice(6124.38, 'EUR'));
    }

    public function test_round_usd(): void
    {
        // >= 1000: increment 100, ceiling
        $this->assertEquals(6200, $this->calculator->roundPrice(6124.38, 'USD'));
    }

    public function test_round_other_currency_no_rounding(): void
    {
        $this->assertEquals(6124.38, $this->calculator->roundPrice(6124.38, 'GBP'));
    }
}
