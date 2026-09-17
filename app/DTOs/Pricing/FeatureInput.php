<?php

namespace App\DTOs\Pricing;

use App\Enums\ComplexityLevel;
use App\Enums\EstimationSource;
use App\Models\Feature;

readonly class FeatureInput
{
    public function __construct(
        public string $name,
        public ?string $description,
        public int $quantity,
        public float $estimatedHours,
        public ComplexityLevel $complexity,
        public string $priority,
        public EstimationSource $estimationSource,
    ) {}

    public static function fromFeature(Feature $feature): self
    {
        return new self(
            name: $feature->name,
            description: $feature->description,
            quantity: $feature->quantity,
            estimatedHours: (float) $feature->estimated_hours,
            complexity: $feature->complexity instanceof ComplexityLevel
                ? $feature->complexity
                : ComplexityLevel::tryFrom((string) $feature->complexity) ?? ComplexityLevel::Normal,
            priority: $feature->priority,
            estimationSource: $feature->estimation_source instanceof EstimationSource
                ? $feature->estimation_source
                : EstimationSource::tryFrom((string) $feature->estimation_source) ?? EstimationSource::Human,
        );
    }

    public function getTotalHours(): float
    {
        return $this->estimatedHours * $this->quantity;
    }
}
