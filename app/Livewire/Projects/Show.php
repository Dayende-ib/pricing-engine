<?php

namespace App\Livewire\Projects;

use App\DTOs\Pricing\PricingInput;
use App\Models\ExternalCost;
use App\Models\Feature;
use App\Models\PricingAdjustment;
use App\Models\Project;
use App\Services\Pricing\PricingEngine;
use Livewire\Component;

class Show extends Component
{
    public Project $project;

    public string $activeTab = 'overview';

    public bool $showFeatureModal = false;

    public bool $showExternalCostModal = false;

    public bool $showAdjustmentModal = false;

    public ?Feature $editingFeature = null;

    public ?ExternalCost $editingExternalCost = null;

    public ?PricingAdjustment $editingAdjustment = null;

    public string $feature_name = '';

    public ?string $feature_description = null;

    public int $feature_quantity = 1;

    public float $feature_estimated_hours = 0;

    public string $feature_complexity = 'normal';

    public string $feature_priority = 'normal';

    public string $feature_estimation_source = 'human';

    public string $external_cost_name = '';

    public ?string $external_cost_description = null;

    public float $external_cost_amount = 0;

    public string $external_cost_frequency = 'one_time';

    public int $external_cost_quantity = 1;

    public string $adjustment_name = '';

    public ?string $adjustment_description = null;

    public string $adjustment_type = 'percentage';

    public float $adjustment_value = 0;

    public ?string $adjustment_reason = null;

    protected $listeners = ['refreshProject' => '$refresh'];

    public function mount(Project $project): void
    {
        $this->project = $project->load([
            'features',
            'externalCosts',
            'pricingAdjustments',
            'pricingProfile',
            'quotes.items',
            'aiAnalyses',
        ]);
    }

    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function openFeatureModal(?Feature $feature = null): void
    {
        $this->resetFeatureForm();
        if ($feature) {
            $this->editingFeature = $feature;
            $this->feature_name = $feature->name;
            $this->feature_description = $feature->description;
            $this->feature_quantity = $feature->quantity;
            $this->feature_estimated_hours = (float) $feature->estimated_hours;
            $this->feature_complexity = $feature->complexity->value;
            $this->feature_priority = $feature->priority;
            $this->feature_estimation_source = $feature->estimation_source->value;
        }
        $this->showFeatureModal = true;
    }

    public function openExternalCostModal(?ExternalCost $externalCost = null): void
    {
        $this->resetExternalCostForm();
        if ($externalCost) {
            $this->editingExternalCost = $externalCost;
            $this->external_cost_name = $externalCost->name;
            $this->external_cost_description = $externalCost->description;
            $this->external_cost_amount = (float) $externalCost->amount;
            $this->external_cost_frequency = $externalCost->frequency->value;
            $this->external_cost_quantity = $externalCost->quantity;
        }
        $this->showExternalCostModal = true;
    }

    public function openAdjustmentModal(?PricingAdjustment $adjustment = null): void
    {
        $this->resetAdjustmentForm();
        if ($adjustment) {
            $this->editingAdjustment = $adjustment;
            $this->adjustment_name = $adjustment->name;
            $this->adjustment_description = $adjustment->description;
            $this->adjustment_type = $adjustment->type->value;
            $this->adjustment_value = (float) $adjustment->value;
            $this->adjustment_reason = $adjustment->reason;
        }
        $this->showAdjustmentModal = true;
    }

    public function saveFeature(): void
    {
        $this->validate([
            'feature_name' => 'required|string|max:255',
            'feature_description' => 'nullable|string',
            'feature_quantity' => 'required|integer|min:1',
            'feature_estimated_hours' => 'required|numeric|min:0',
            'feature_complexity' => 'required|in:low,normal,high,very_high',
            'feature_priority' => 'required|in:low,normal,high,critical',
            'feature_estimation_source' => 'required|in:ai,human,template,historical',
        ]);

        if ($this->editingFeature) {
            $this->editingFeature->update([
                'name' => $this->feature_name,
                'description' => $this->feature_description,
                'quantity' => $this->feature_quantity,
                'estimated_hours' => $this->feature_estimated_hours,
                'complexity' => $this->feature_complexity,
                'priority' => $this->feature_priority,
                'estimation_source' => $this->feature_estimation_source,
            ]);
            session()->flash('success', 'Fonctionnalité mise à jour');
        } else {
            $this->project->features()->create([
                'name' => $this->feature_name,
                'description' => $this->feature_description,
                'quantity' => $this->feature_quantity,
                'estimated_hours' => $this->feature_estimated_hours,
                'complexity' => $this->feature_complexity,
                'priority' => $this->feature_priority,
                'estimation_source' => $this->feature_estimation_source,
            ]);
            session()->flash('success', 'Fonctionnalité ajoutée');
        }

        $this->closeModals();
        $this->refreshProject();
    }

    public function saveExternalCost(): void
    {
        $this->validate([
            'external_cost_name' => 'required|string|max:255',
            'external_cost_description' => 'nullable|string',
            'external_cost_amount' => 'required|numeric|min:0',
            'external_cost_frequency' => 'required|in:one_time,monthly,yearly',
            'external_cost_quantity' => 'required|integer|min:1',
        ]);

        if ($this->editingExternalCost) {
            $this->editingExternalCost->update([
                'name' => $this->external_cost_name,
                'description' => $this->external_cost_description,
                'amount' => $this->external_cost_amount,
                'frequency' => $this->external_cost_frequency,
                'quantity' => $this->external_cost_quantity,
            ]);
            session()->flash('success', 'Coût externe mis à jour');
        } else {
            $this->project->externalCosts()->create([
                'name' => $this->external_cost_name,
                'description' => $this->external_cost_description,
                'amount' => $this->external_cost_amount,
                'frequency' => $this->external_cost_frequency,
                'quantity' => $this->external_cost_quantity,
            ]);
            session()->flash('success', 'Coût externe ajouté');
        }

        $this->closeModals();
        $this->refreshProject();
    }

    public function saveAdjustment(): void
    {
        $this->validate([
            'adjustment_name' => 'required|string|max:255',
            'adjustment_description' => 'nullable|string',
            'adjustment_type' => 'required|in:percentage,fixed',
            'adjustment_value' => 'required|numeric',
            'adjustment_reason' => 'nullable|string',
        ]);

        if ($this->editingAdjustment) {
            $this->editingAdjustment->update([
                'name' => $this->adjustment_name,
                'description' => $this->adjustment_description,
                'type' => $this->adjustment_type,
                'value' => $this->adjustment_value,
                'reason' => $this->adjustment_reason,
            ]);
            session()->flash('success', 'Ajustement mis à jour');
        } else {
            $this->project->pricingAdjustments()->create([
                'name' => $this->adjustment_name,
                'description' => $this->adjustment_description,
                'type' => $this->adjustment_type,
                'value' => $this->adjustment_value,
                'reason' => $this->adjustment_reason,
            ]);
            session()->flash('success', 'Ajustement ajouté');
        }

        $this->closeModals();
        $this->refreshProject();
    }

    public function deleteFeature(Feature $feature): void
    {
        $feature->delete();
        session()->flash('success', 'Fonctionnalité supprimée');
        $this->refreshProject();
    }

    public function deleteExternalCost(ExternalCost $externalCost): void
    {
        $externalCost->delete();
        session()->flash('success', 'Coût externe supprimé');
        $this->refreshProject();
    }

    public function deleteAdjustment(PricingAdjustment $adjustment): void
    {
        $adjustment->delete();
        session()->flash('success', 'Ajustement supprimé');
        $this->refreshProject();
    }

    public function calculatePricing(): void
    {
        if (! $this->project->pricingProfile) {
            session()->flash('error', 'Aucun profil tarifaire associé au projet');

            return;
        }

        $input = PricingInput::fromProject($this->project);
        $engine = app(PricingEngine::class);
        $result = $engine->calculate($input);

        $this->project->update([
            'status' => 'priced',
        ]);

        session()->flash('success', 'Calcul effectué ! Prix cible: '.number_format($result->targetPrice, 0, ',', ' ').' '.$result->currency);
        $this->refreshProject();
    }

    public function generateQuote(): void
    {
        $input = PricingInput::fromProject($this->project);
        $engine = app(PricingEngine::class);
        $result = $engine->calculate($input);

        // Generate quote number
        $year = now()->year;
        $lastQuote = $this->project->quotes()->latest('id')->first();
        $sequence = $lastQuote ? (int) substr($lastQuote->quote_number, -4) + 1 : 1;
        $quoteNumber = 'DEV-'.$year.'-'.str_pad($sequence, 4, '0', STR_PAD_LEFT);

        $quote = $this->project->quotes()->create([
            'quote_number' => $quoteNumber,
            'status' => 'draft',
            'subtotal' => $result->baseDevelopmentCost,
            'adjustments_total' => $result->adjustmentsTotal,
            'external_costs_total' => $result->externalCostsTotal,
            'discount' => 0,
            'total' => $result->targetPrice,
            'currency' => $result->currency,
            'valid_until' => now()->addDays(30),
        ]);

        // Create quote items from features
        foreach ($this->project->features as $feature) {
            $hours = $feature->estimated_hours * $feature->quantity;
            $rate = (float) $this->project->pricingProfile->hourly_rate;
            $unitPrice = $hours * $rate;

            $quote->items()->create([
                'feature_id' => $feature->id,
                'description' => $feature->name.($feature->description ? ' - '.$feature->description : ''),
                'quantity' => 1,
                'unit_price' => $unitPrice,
                'total' => $unitPrice,
            ]);
        }

        // Add external costs as items
        foreach ($this->project->externalCosts as $cost) {
            $quote->items()->create([
                'feature_id' => null,
                'description' => $cost->name.($cost->description ? ' - '.$cost->description : ''),
                'quantity' => $cost->quantity,
                'unit_price' => (float) $cost->amount,
                'total' => (float) $cost->amount * $cost->quantity,
            ]);
        }

        // Add adjustments as items
        foreach ($this->project->pricingAdjustments as $adj) {
            $quote->items()->create([
                'feature_id' => null,
                'description' => $adj->name.($adj->description ? ' - '.$adj->description : ''),
                'quantity' => 1,
                'unit_price' => $adj->type === 'percentage' ? 0 : (float) $adj->value,
                'total' => $adj->type === 'percentage' ? 0 : (float) $adj->value,
            ]);
        }

        $this->project->update(['status' => 'quoted']);

        session()->flash('success', 'Devis généré : '.$quoteNumber);
        $this->refreshProject();
    }

    public function closeModals(): void
    {
        $this->showFeatureModal = false;
        $this->showExternalCostModal = false;
        $this->showAdjustmentModal = false;
        $this->editingFeature = null;
        $this->editingExternalCost = null;
        $this->editingAdjustment = null;
        $this->resetFeatureForm();
        $this->resetExternalCostForm();
        $this->resetAdjustmentForm();
    }

    protected function resetFeatureForm(): void
    {
        $this->feature_name = '';
        $this->feature_description = null;
        $this->feature_quantity = 1;
        $this->feature_estimated_hours = 0;
        $this->feature_complexity = 'normal';
        $this->feature_priority = 'normal';
        $this->feature_estimation_source = 'human';
    }

    protected function resetExternalCostForm(): void
    {
        $this->external_cost_name = '';
        $this->external_cost_description = null;
        $this->external_cost_amount = 0;
        $this->external_cost_frequency = 'one_time';
        $this->external_cost_quantity = 1;
    }

    protected function resetAdjustmentForm(): void
    {
        $this->adjustment_name = '';
        $this->adjustment_description = null;
        $this->adjustment_type = 'percentage';
        $this->adjustment_value = 0;
        $this->adjustment_reason = null;
    }

    public function refreshProject(): void
    {
        $this->project = $this->project->fresh([
            'features',
            'externalCosts',
            'pricingAdjustments',
            'pricingProfile',
            'quotes.items',
            'aiAnalyses',
        ]);
    }

    public function getPricingResultProperty()
    {
        if (! $this->project->pricingProfile) {
            return null;
        }

        $input = PricingInput::fromProject($this->project);
        $engine = app(PricingEngine::class);

        return $engine->calculate($input);
    }

    public function render()
    {
        return view('livewire.projects.show');
    }
}
