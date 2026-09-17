<?php

namespace App\Models;

use App\Enums\ComplexityLevel;
use App\Enums\ProjectStatus;
use App\Enums\RiskLevel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'pricing_profile_id',
    'name',
    'client_name',
    'client_email',
    'description',
    'project_type',
    'status',
    'complexity',
    'risk_level',
    'deadline',
    'budget_min',
    'budget_max',
    'currency',
])]
class Project extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'deadline' => 'date',
            'budget_min' => 'decimal:2',
            'budget_max' => 'decimal:2',
            'status' => ProjectStatus::class,
            'complexity' => ComplexityLevel::class,
            'risk_level' => RiskLevel::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pricingProfile(): BelongsTo
    {
        return $this->belongsTo(PricingProfile::class);
    }

    public function features(): HasMany
    {
        return $this->hasMany(Feature::class);
    }

    public function externalCosts(): HasMany
    {
        return $this->hasMany(ExternalCost::class);
    }

    public function pricingAdjustments(): HasMany
    {
        return $this->hasMany(PricingAdjustment::class);
    }

    public function aiAnalyses(): HasMany
    {
        return $this->hasMany(AIAnalysis::class);
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }

    public function getComplexityFactor(): float
    {
        $complexity = $this->complexity;

        if (! $complexity instanceof ComplexityLevel) {
            return 1.10;
        }

        return $complexity->factor();
    }

    public function getRiskFactor(): float
    {
        $risk = $this->risk_level;

        if (! $risk instanceof RiskLevel) {
            return 1.00;
        }

        return $risk->factor();
    }

    public function isEditable(): bool
    {
        $status = $this->status;

        if (! $status instanceof ProjectStatus) {
            return false;
        }

        return in_array($status, [
            ProjectStatus::Draft,
            ProjectStatus::Analyzing,
            ProjectStatus::Review,
        ], true);
    }
}
