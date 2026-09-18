<?php

namespace App\Livewire\Projects;

use App\Enums\ProjectStatus;
use App\Models\PricingProfile;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    public int $currentStep = 1;

    public int $totalSteps = 4;

    public string $name = '';

    public string $client_name = '';

    public string $client_email = '';

    public string $description = '';

    public string $project_type = 'web_app';

    public ?string $deadline = null;

    public ?float $budget_min = null;

    public ?float $budget_max = null;

    public string $currency = 'XOF';

    public ?int $pricing_profile_id = null;

    protected $rules = [
        'name' => 'required|string|max:255',
        'client_name' => 'nullable|string|max:255',
        'client_email' => 'nullable|email|max:255',
        'description' => 'nullable|string',
        'project_type' => 'required|string',
        'deadline' => 'nullable|date|after:today',
        'budget_min' => 'nullable|numeric|min:0',
        'budget_max' => 'nullable|numeric|min:0|gte:budget_min',
        'currency' => 'required|string|size:3',
        'pricing_profile_id' => 'required|exists:pricing_profiles,id',
    ];

    public function mount(): void
    {
        $defaultProfile = PricingProfile::where('user_id', auth()->id())
            ->where('is_default', true)
            ->first();

        if ($defaultProfile) {
            $this->pricing_profile_id = $defaultProfile->id;
        } else {
            $firstProfile = PricingProfile::where('user_id', auth()->id())->first();
            if ($firstProfile) {
                $this->pricing_profile_id = $firstProfile->id;
            }
        }
    }

    public function nextStep(): void
    {
        $this->validateStep($this->currentStep);

        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
        }
    }

    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    protected function validateStep(int $step): void
    {
        switch ($step) {
            case 1:
                $this->validate([
                    'name' => 'required|string|max:255',
                    'client_name' => 'nullable|string|max:255',
                    'client_email' => 'nullable|email|max:255',
                    'description' => 'nullable|string',
                    'project_type' => 'required|string',
                    'deadline' => 'nullable|date|after:today',
                    'budget_min' => 'nullable|numeric|min:0',
                    'budget_max' => 'nullable|numeric|min:0|gte:budget_min',
                ]);
                break;
            case 2:
                $this->validate([
                    'currency' => 'required|string|size:3',
                    'pricing_profile_id' => 'required|exists:pricing_profiles,id',
                ]);
                break;
        }
    }

    public function save(): RedirectResponse
    {
        $this->validate();

        $project = Project::create([
            'user_id' => auth()->id(),
            'pricing_profile_id' => $this->pricing_profile_id,
            'name' => $this->name,
            'client_name' => $this->client_name,
            'client_email' => $this->client_email,
            'description' => $this->description,
            'project_type' => $this->project_type,
            'status' => ProjectStatus::Draft->value,
            'complexity' => 'normal',
            'risk_level' => 'low',
            'deadline' => $this->deadline,
            'budget_min' => $this->budget_min,
            'budget_max' => $this->budget_max,
            'currency' => $this->currency,
        ]);

        session()->flash('success', 'Projet créé avec succès !');

        return redirect()->route('projects.show', $project);
    }

    public function getProgressProperty(): int
    {
        return (int) round(($this->currentStep / $this->totalSteps) * 100);
    }

    public function getPricingProfilesProperty()
    {
        return PricingProfile::where('user_id', auth()->id())->get();
    }

    public function render()
    {
        $progress = (int) round(($this->currentStep / $this->totalSteps) * 100);

        return view('livewire.projects.create', ['progress' => $progress]);
    }
}
