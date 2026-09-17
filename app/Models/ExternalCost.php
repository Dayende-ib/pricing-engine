<?php

namespace App\Models;

use App\Enums\ExternalCostFrequency;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'project_id',
    'name',
    'description',
    'amount',
    'frequency',
    'quantity',
])]
class ExternalCost extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'quantity' => 'integer',
            'frequency' => ExternalCostFrequency::class,
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function getTotalCost(int $months = 1): float
    {
        $baseCost = $this->amount * $this->quantity;
        $frequency = $this->frequency;

        if (! $frequency instanceof ExternalCostFrequency) {
            return $baseCost;
        }

        return match ($frequency) {
            ExternalCostFrequency::OneTime => $baseCost,
            ExternalCostFrequency::Monthly => $baseCost * $months,
            ExternalCostFrequency::Yearly => $baseCost * ceil($months / 12),
        };
    }
}
