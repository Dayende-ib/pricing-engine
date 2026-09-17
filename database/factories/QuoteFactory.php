<?php

namespace Database\Factories;

use App\Enums\QuoteStatus;
use App\Models\Project;
use App\Models\Quote;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Quote>
 */
class QuoteFactory extends Factory
{
    protected $model = Quote::class;

    public function definition(): array
    {
        $subtotal = fake()->numberBetween(200000, 5000000);
        $adjustments = fake()->numberBetween(0, 500000);
        $externalCosts = fake()->numberBetween(0, 300000);
        $discount = fake()->numberBetween(0, 100000);
        $total = $subtotal + $adjustments + $externalCosts - $discount;

        return [
            'project_id' => Project::factory(),
            'quote_number' => 'DEV-'.date('Y').'-'.str_pad((string) fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'status' => fake()->randomElement(QuoteStatus::cases()),
            'subtotal' => $subtotal,
            'adjustments_total' => $adjustments,
            'external_costs_total' => $externalCosts,
            'discount' => $discount,
            'total' => $total,
            'currency' => 'XOF',
            'valid_until' => fake()->dateTimeBetween('+15 days', '+90 days'),
            'notes' => fake()->optional()->paragraph(),
        ];
    }
}
