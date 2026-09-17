<?php

namespace Database\Seeders;

use App\Models\AIAnalysis;
use App\Models\ExternalCost;
use App\Models\Feature;
use App\Models\PricingAdjustment;
use App\Models\PricingProfile;
use App\Models\PricingRate;
use App\Models\Project;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Développeur Demo',
            'email' => 'demo@example.com',
        ]);

        $pricingProfile = PricingProfile::factory()->default()->create([
            'user_id' => $user->id,
            'name' => 'Mon profil principal',
            'hourly_rate' => 4000,
            'currency' => 'XOF',
            'minimum_margin' => 0.20,
            'target_margin' => 0.35,
            'premium_margin' => 0.50,
            'default_risk_reserve' => 0.10,
        ]);

        PricingRate::factory()->count(5)->create([
            'pricing_profile_id' => $pricingProfile->id,
        ]);

        $projects = Project::factory()->count(3)->create([
            'user_id' => $user->id,
            'pricing_profile_id' => $pricingProfile->id,
        ]);

        foreach ($projects as $project) {
            Feature::factory()->count(fake()->numberBetween(3, 8))->create([
                'project_id' => $project->id,
            ]);

            ExternalCost::factory()->count(fake()->numberBetween(1, 4))->create([
                'project_id' => $project->id,
            ]);

            PricingAdjustment::factory()->count(fake()->numberBetween(0, 3))->create([
                'project_id' => $project->id,
            ]);

            AIAnalysis::factory()->create([
                'project_id' => $project->id,
            ]);

            $quote = Quote::factory()->create([
                'project_id' => $project->id,
            ]);

            QuoteItem::factory()->count(fake()->numberBetween(3, 6))->create([
                'quote_id' => $quote->id,
            ]);
        }

        $demoProject = Project::factory()->create([
            'user_id' => $user->id,
            'pricing_profile_id' => $pricingProfile->id,
            'name' => 'Application mobile de gestion de poissonnerie',
            'client_name' => 'Poissonnerie Moderne',
            'client_email' => 'contact@poissonnerie-modern.com',
            'description' => 'Développement d\'une application mobile complète pour la gestion d\'une poissonnerie : suivi des stocks, gestion des commandes clients, facturation, tableau de bord analytique et notifications.',
            'project_type' => 'mobile_app',
            'status' => 'review',
            'complexity' => 'high',
            'risk_level' => 'medium',
            'deadline' => now()->addMonths(3),
            'budget_min' => 2000000,
            'budget_max' => 5000000,
        ]);

        $demoFeatures = [
            ['name' => 'Authentification', 'description' => 'Inscription/connexion utilisateurs (clients, vendeurs, admin)', 'estimated_hours' => 16, 'complexity' => 'normal', 'quantity' => 1, 'priority' => 'critical'],
            ['name' => 'Catalogue produits', 'description' => 'Gestion poissons/crustacés avec catégories, prix, unités', 'estimated_hours' => 24, 'complexity' => 'normal', 'quantity' => 1, 'priority' => 'high'],
            ['name' => 'Gestion stock temps réel', 'description' => 'Mise à jour stock à chaque vente, alertes seuils bas', 'estimated_hours' => 20, 'complexity' => 'high', 'quantity' => 1, 'priority' => 'high'],
            ['name' => 'Commandes clients', 'description' => 'Panier, validation, suivi statut, historique', 'estimated_hours' => 32, 'complexity' => 'high', 'quantity' => 1, 'priority' => 'critical'],
            ['name' => 'Facturation', 'description' => 'Génération factures PDF, numérotation, TVA', 'estimated_hours' => 12, 'complexity' => 'normal', 'quantity' => 1, 'priority' => 'high'],
            ['name' => 'Tableau de bord analytics', 'description' => 'Ventes, stocks, clients, top produits, CA par période', 'estimated_hours' => 18, 'complexity' => 'high', 'quantity' => 1, 'priority' => 'normal'],
            ['name' => 'Notifications push/SMS', 'description' => 'Alertes stock, confirmation commande, promo', 'estimated_hours' => 14, 'complexity' => 'normal', 'quantity' => 1, 'priority' => 'normal'],
            ['name' => 'Mode hors-ligne', 'description' => 'Synchronisation données quand connexion restaurée', 'estimated_hours' => 28, 'complexity' => 'very_high', 'quantity' => 1, 'priority' => 'normal'],
        ];

        foreach ($demoFeatures as $feature) {
            Feature::create([
                'project_id' => $demoProject->id,
                'name' => $feature['name'],
                'description' => $feature['description'],
                'estimated_hours' => $feature['estimated_hours'],
                'complexity' => $feature['complexity'],
                'quantity' => $feature['quantity'],
                'priority' => $feature['priority'],
                'estimation_source' => 'ai',
            ]);
        }

        ExternalCost::factory()->create([
            'project_id' => $demoProject->id,
            'name' => 'Hébergement serveur',
            'description' => 'Serveur VPS mensuel pour API et base de données',
            'amount' => 35000,
            'frequency' => 'monthly',
            'quantity' => 1,
        ]);

        ExternalCost::factory()->create([
            'project_id' => $demoProject->id,
            'name' => 'API SMS',
            'description' => 'Envoi notifications SMS (Twilio/Orange)',
            'amount' => 25000,
            'frequency' => 'monthly',
            'quantity' => 1,
        ]);

        ExternalCost::factory()->create([
            'project_id' => $demoProject->id,
            'name' => 'Compte développeur Apple/Google',
            'description' => 'Publication App Store / Play Store (annuel)',
            'amount' => 150000,
            'frequency' => 'yearly',
            'quantity' => 1,
        ]);

        PricingAdjustment::factory()->create([
            'project_id' => $demoProject->id,
            'name' => 'Complexité technique',
            'description' => 'Mode hors-ligne et synchronisation complexe',
            'type' => 'percentage',
            'value' => 15,
            'reason' => 'Architecture offline-first non standard',
        ]);

        PricingAdjustment::factory()->create([
            'project_id' => $demoProject->id,
            'name' => 'Délai court',
            'description' => 'Livraison demandée sous 3 mois',
            'type' => 'percentage',
            'value' => 10,
            'reason' => 'Planning serré, équipe dédiée nécessaire',
        ]);

        AIAnalysis::factory()->create([
            'project_id' => $demoProject->id,
            'provider' => 'OpenRouter',
            'model' => 'anthropic/claude-3.5-sonnet',
            'prompt_version' => 'v1.0',
            'status' => 'completed',
            'confidence' => 0.85,
        ]);
    }
}
