<?php

namespace Database\Factories;

use App\Models\PricingProfile;
use App\Models\PricingRate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PricingRate>
 */
class PricingRateFactory extends Factory
{
    protected $model = PricingRate::class;

    public function definition(): array
    {
        return [
            'pricing_profile_id' => PricingProfile::factory(),
            'name' => fake()->randomElement(['Laravel', 'Flutter', 'UI/UX', 'Backend', 'Frontend', 'DevOps', 'Consulting', 'Maintenance']),
            'rate' => fake()->numberBetween(2000, 10000),
            'unit' => 'hour',
        ];
    }
}
