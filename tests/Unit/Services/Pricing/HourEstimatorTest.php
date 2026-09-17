<?php

namespace Tests\Unit\Services\Pricing;

use App\DTOs\Pricing\FeatureInput;
use App\Enums\ComplexityLevel;
use App\Enums\EstimationSource;
use App\Services\Pricing\HourEstimator;
use PHPUnit\Framework\TestCase;

class HourEstimatorTest extends TestCase
{
    private HourEstimator $estimator;

    protected function setUp(): void
    {
        $this->estimator = new HourEstimator;
    }

    public function test_calculate_base_hours(): void
    {
        $features = [
            new FeatureInput('Auth', 'Authentication', 1, 8, ComplexityLevel::Normal, 'high', EstimationSource::Human),
            new FeatureInput('Products', 'Product management', 1, 12, ComplexityLevel::Normal, 'high', EstimationSource::Human),
            new FeatureInput('Stock', 'Stock management', 1, 15, ComplexityLevel::Normal, 'high', EstimationSource::Human),
        ];

        $total = $this->estimator->calculateBaseHours($features);

        $this->assertEquals(35.0, $total); // 8 + 12 + 15
    }

    public function test_calculate_base_hours_with_quantity(): void
    {
        $features = [
            new FeatureInput('Auth', 'Authentication', 2, 8, ComplexityLevel::Normal, 'high', EstimationSource::Human),
            new FeatureInput('API', 'API endpoints', 3, 5, ComplexityLevel::Normal, 'high', EstimationSource::Human),
        ];

        $total = $this->estimator->calculateBaseHours($features);

        $this->assertEquals(31.0, $total); // 8*2 + 5*3 = 16 + 15
    }

    public function test_empty_features(): void
    {
        $total = $this->estimator->calculateBaseHours([]);
        $this->assertEquals(0.0, $total);
    }

    public function test_zero_hours_feature(): void
    {
        $features = [
            new FeatureInput('Auth', 'Authentication', 1, 0, ComplexityLevel::Normal, 'high', EstimationSource::Human),
            new FeatureInput('Products', 'Product management', 1, 10, ComplexityLevel::Normal, 'high', EstimationSource::Human),
        ];

        $total = $this->estimator->calculateBaseHours($features);
        $this->assertEquals(10.0, $total);
    }
}
