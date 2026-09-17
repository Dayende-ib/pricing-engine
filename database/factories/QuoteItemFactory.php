<?php

namespace Database\Factories;

use App\Models\Feature;
use App\Models\Quote;
use App\Models\QuoteItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QuoteItem>
 */
class QuoteItemFactory extends Factory
{
    protected $model = QuoteItem::class;

    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 5);
        $unitPrice = fake()->numberBetween(50000, 500000);

        return [
            'quote_id' => Quote::factory(),
            'feature_id' => Feature::factory(),
            'description' => fake()->sentence(6),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total' => $quantity * $unitPrice,
        ];
    }
}
