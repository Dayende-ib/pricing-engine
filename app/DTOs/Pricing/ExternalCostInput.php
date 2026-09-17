<?php

namespace App\DTOs\Pricing;

use App\Enums\ExternalCostFrequency;
use App\Models\ExternalCost;

readonly class ExternalCostInput
{
    public function __construct(
        public string $name,
        public ?string $description,
        public float $amount,
        public ExternalCostFrequency $frequency,
        public int $quantity,
    ) {}

    public static function fromExternalCost(ExternalCost $externalCost): self
    {
        return new self(
            name: $externalCost->name,
            description: $externalCost->description,
            amount: (float) $externalCost->amount,
            frequency: $externalCost->frequency instanceof ExternalCostFrequency
                ? $externalCost->frequency
                : ExternalCostFrequency::tryFrom((string) $externalCost->frequency) ?? ExternalCostFrequency::OneTime,
            quantity: $externalCost->quantity,
        );
    }

    public function getTotalCost(int $months): float
    {
        $baseCost = $this->amount * $this->quantity;

        return match ($this->frequency) {
            ExternalCostFrequency::OneTime => $baseCost,
            ExternalCostFrequency::Monthly => $baseCost * $months,
            ExternalCostFrequency::Yearly => $baseCost * ceil($months / 12),
        };
    }
}
