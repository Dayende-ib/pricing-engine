<?php

namespace Database\Factories;

use App\Enums\AdjustmentType;
use App\Models\PricingAdjustment;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PricingAdjustment>
 */
class PricingAdjustmentFactory extends Factory
{
    protected $model = PricingAdjustment::class;

    public function definition(): array
    {
        $adjustments = [
            'Urgence' => 'Délai très court demandé par le client',
            'Complexité technique' => 'Architecture complexe non prévue',
            'Spécifications floues' => 'Besoin de clarification importante',
            'Client exigeant' => 'Nombreuses itérations attendues',
            'Délai court' => 'Livraison en urgence',
            'Support étendu' => 'Support post-livraison inclus',
            'Maintenance' => 'Maintenance sur 12 mois',
            'Formation' => 'Formation équipe client',
        ];

        $adjustment = fake()->randomElement(array_keys($adjustments));
        $type = fake()->randomElement(AdjustmentType::cases());

        return [
            'project_id' => Project::factory(),
            'name' => $adjustment,
            'description' => $adjustments[$adjustment],
            'type' => $type,
            'value' => $type === AdjustmentType::Percentage
                ? fake()->randomFloat(2, 5, 30)
                : fake()->numberBetween(50000, 500000),
            'reason' => $adjustments[$adjustment],
        ];
    }
}
