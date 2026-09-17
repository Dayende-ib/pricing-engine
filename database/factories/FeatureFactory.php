<?php

namespace Database\Factories;

use App\Enums\ComplexityLevel;
use App\Enums\EstimationSource;
use App\Models\Feature;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Feature>
 */
class FeatureFactory extends Factory
{
    protected $model = Feature::class;

    public function definition(): array
    {
        $features = [
            'Authentification' => 'Inscription et connexion utilisateur',
            'Gestion des produits' => 'CRUD produits avec catégories',
            'Gestion du stock' => 'Suivi des stocks et alertes',
            'Commandes' => 'Processus de commande complet',
            'Paiement' => 'Intégration paiement en ligne',
            'Tableau de bord' => 'Statistiques et rapports',
            'Notifications' => 'Système de notifications',
            'Recherche' => 'Recherche avancée avec filtres',
            'Export' => 'Export PDF/Excel des données',
            'API REST' => 'API pour intégrations tierces',
        ];

        $feature = fake()->randomElement(array_keys($features));

        return [
            'project_id' => Project::factory(),
            'name' => $feature,
            'description' => $features[$feature],
            'quantity' => fake()->numberBetween(1, 3),
            'estimated_hours' => fake()->numberBetween(2, 40),
            'complexity' => fake()->randomElement(ComplexityLevel::cases()),
            'priority' => fake()->randomElement(['low', 'normal', 'high', 'critical']),
            'estimation_source' => fake()->randomElement(EstimationSource::cases()),
        ];
    }
}
