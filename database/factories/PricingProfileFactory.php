<?php

namespace Database\Factories;

use App\Models\PricingProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PricingProfile>
 */
class PricingProfileFactory extends Factory
{
    protected $model = PricingProfile::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->randomElement(['Profil principal', 'Profil freelance', 'Profil agence', 'Profil consulting']),
            'currency' => 'XOF',
            'hourly_rate' => fake()->numberBetween(2000, 10000),
            'minimum_margin' => 0.20,
            'target_margin' => 0.35,
            'premium_margin' => 0.50,
            'default_risk_reserve' => 0.10,
            'default_revision_hours' => fake()->numberBetween(0, 20),
            'is_default' => false,
        ];
    }

    public function default(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Mon profil principal',
            'is_default' => true,
            'hourly_rate' => 4000,
        ]);
    }
}
