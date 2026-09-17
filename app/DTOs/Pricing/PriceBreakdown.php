<?php

namespace App\DTOs\Pricing;

readonly class PriceBreakdown
{
    public function __construct(
        public string $label,
        public float $value,
        public ?string $detail = null,
    ) {}
}
