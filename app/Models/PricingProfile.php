<?php

namespace App\Models;

use App\Enums\ComplexityLevel;
use App\Enums\RiskLevel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'name',
    'currency',
    'hourly_rate',
    'minimum_margin',
    'target_margin',
    'premium_margin',
    'default_risk_reserve',
    'default_revision_hours',
    'is_default',
])]
class PricingProfile extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'hourly_rate' => 'decimal:2',
            'minimum_margin' => 'decimal:4',
            'target_margin' => 'decimal:4',
            'premium_margin' => 'decimal:4',
            'default_risk_reserve' => 'decimal:4',
            'default_revision_hours' => 'decimal:2',
            'is_default' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function rates(): HasMany
    {
        return $this->hasMany(PricingRate::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function getComplexityFactor(ComplexityLevel $complexity): float
    {
        return $complexity->factor();
    }

    public function getRiskFactor(RiskLevel $risk): float
    {
        return $risk->factor();
    }
}
