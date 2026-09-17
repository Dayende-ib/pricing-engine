<?php

namespace App\Models;

use App\Enums\ComplexityLevel;
use App\Enums\EstimationSource;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'project_id',
    'name',
    'description',
    'quantity',
    'estimated_hours',
    'complexity',
    'priority',
    'estimation_source',
])]
class Feature extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'estimated_hours' => 'decimal:2',
            'complexity' => ComplexityLevel::class,
            'estimation_source' => EstimationSource::class,
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function getTotalHours(): float
    {
        return $this->estimated_hours * $this->quantity;
    }
}
