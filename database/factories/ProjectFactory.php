<?php

namespace Database\Factories;

use App\Enums\ComplexityLevel;
use App\Enums\ProjectStatus;
use App\Enums\RiskLevel;
use App\Models\PricingProfile;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        $status = fake()->randomElement(ProjectStatus::cases());

        return [
            'user_id' => User::factory(),
            'pricing_profile_id' => PricingProfile::factory(),
            'name' => fake()->sentence(3),
            'client_name' => fake()->company(),
            'client_email' => fake()->companyEmail(),
            'description' => fake()->paragraphs(2, true),
            'project_type' => fake()->randomElement(['web_app', 'mobile_app', 'api', 'ecommerce', 'dashboard', 'landing_page']),
            'status' => $status,
            'complexity' => fake()->randomElement(ComplexityLevel::cases()),
            'risk_level' => fake()->randomElement(RiskLevel::cases()),
            'deadline' => fake()->optional()->dateTimeBetween('now', '+6 months'),
            'budget_min' => fake()->optional()->numberBetween(100000, 1000000),
            'budget_max' => fake()->optional()->numberBetween(1000000, 5000000),
            'currency' => 'XOF',
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ProjectStatus::Draft,
        ]);
    }

    public function withProfile(PricingProfile $profile): static
    {
        return $this->state(fn (array $attributes) => [
            'pricing_profile_id' => $profile->id,
        ]);
    }
}
