<?php

namespace Database\Factories;

use App\Enums\ExternalCostFrequency;
use App\Models\ExternalCost;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExternalCost>
 */
class ExternalCostFactory extends Factory
{
    protected $model = ExternalCost::class;

    public function definition(): array
    {
        $costs = [
            'Hébergement' => 'Serveur cloud mensuel',
            'Nom de domaine' => 'Renouvellement annuel domaine',
            'API SMS' => 'Envoi de SMS transactionnels',
            'WhatsApp Business' => 'API WhatsApp Business',
            'Google Maps' => 'API Google Maps Platform',
            'Service email' => 'SendGrid / Mailgun',
            'Licence logiciel' => 'Licence SaaS mensuelle',
            'SSL Certificate' => 'Certificat SSL annuel',
            'CDN' => 'Content Delivery Network',
            'Monitoring' => 'Service de monitoring',
        ];

        $cost = fake()->randomElement(array_keys($costs));

        return [
            'project_id' => Project::factory(),
            'name' => $cost,
            'description' => $costs[$cost],
            'amount' => fake()->numberBetween(5000, 200000),
            'frequency' => fake()->randomElement(ExternalCostFrequency::cases()),
            'quantity' => fake()->numberBetween(1, 5),
        ];
    }
}
