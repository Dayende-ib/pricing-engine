<?php

namespace App\DTOs\Pricing;

use App\Enums\AdjustmentType;
use App\Models\PricingAdjustment;

readonly class AdjustmentInput
{
    public function __construct(
        public string $name,
        public ?string $description,
        public AdjustmentType $type,
        public float $value,
        public ?string $reason,
    ) {}

    public static function fromAdjustment(PricingAdjustment $adjustment): self
    {
        return new self(
            name: $adjustment->name,
            description: $adjustment->description,
            type: $adjustment->type instanceof AdjustmentType
                ? $adjustment->type
                : AdjustmentType::tryFrom((string) $adjustment->type) ?? AdjustmentType::Percentage,
            value: (float) $adjustment->value,
            reason: $adjustment->reason,
        );
    }
}
