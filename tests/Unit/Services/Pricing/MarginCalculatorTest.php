<?php

namespace Tests\Unit\Services\Pricing;

use App\Services\Pricing\MarginCalculator;
use PHPUnit\Framework\TestCase;

class MarginCalculatorTest extends TestCase
{
    private MarginCalculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new MarginCalculator;
    }

    public function test_calculate_prices(): void
    {
        $profile = new class implements \ArrayAccess
        {
            public float $minimum_margin = 0.20;

            public float $target_margin = 0.35;

            public float $premium_margin = 0.50;

            public function offsetExists(mixed $offset): bool
            {
                return isset($this->$offset);
            }

            public function offsetGet(mixed $offset): mixed
            {
                return $this->$offset ?? null;
            }

            public function offsetSet(mixed $offset, mixed $value): void
            {
                $this->$offset = $value;
            }

            public function offsetUnset(mixed $offset): void
            {
                $this->$offset = null;
            }
        };

        $prices = $this->calculator->calculatePrices(100000, $profile);

        // floor = 100000 / 0.8 = 125000
        // target = 100000 / 0.65 = 153846
        // premium = 100000 / 0.5 = 200000
        $this->assertEquals(125000, $prices['floor']);
        $this->assertEquals(153846.15, round($prices['target'], 2));
        $this->assertEquals(200000, $prices['premium']);
    }

    public function test_zero_margin(): void
    {
        $profile = new class implements \ArrayAccess
        {
            public float $minimum_margin = 0.0;

            public float $target_margin = 0.0;

            public float $premium_margin = 0.0;

            public function offsetExists(mixed $offset): bool
            {
                return isset($this->$offset);
            }

            public function offsetGet(mixed $offset): mixed
            {
                return $this->$offset ?? null;
            }

            public function offsetSet(mixed $offset, mixed $value): void
            {
                $this->$offset = $value;
            }

            public function offsetUnset(mixed $offset): void
            {
                $this->$offset = null;
            }
        };

        $prices = $this->calculator->calculatePrices(100000, $profile);

        $this->assertEquals(100000, $prices['floor']);
        $this->assertEquals(100000, $prices['target']);
        $this->assertEquals(100000, $prices['premium']);
    }

    public function test_high_margin(): void
    {
        $profile = new class implements \ArrayAccess
        {
            public float $minimum_margin = 0.80;

            public float $target_margin = 0.90;

            public float $premium_margin = 0.95;

            public function offsetExists(mixed $offset): bool
            {
                return isset($this->$offset);
            }

            public function offsetGet(mixed $offset): mixed
            {
                return $this->$offset ?? null;
            }

            public function offsetSet(mixed $offset, mixed $value): void
            {
                $this->$offset = $value;
            }

            public function offsetUnset(mixed $offset): void
            {
                $this->$offset = null;
            }
        };

        $prices = $this->calculator->calculatePrices(100000, $profile);

        // 80% margin = 100000 / 0.2 = 500000
        // 90% margin = 100000 / 0.1 = 1000000
        // 95% margin = 100000 / 0.05 = 2000000
        $this->assertEqualsWithDelta(500000, $prices['floor'], 0.001);
        $this->assertEqualsWithDelta(1000000, $prices['target'], 0.001);
        $this->assertEqualsWithDelta(2000000, $prices['premium'], 0.001);
    }
}
