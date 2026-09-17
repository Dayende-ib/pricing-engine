<?php

namespace App\Models;

use App\Enums\AdjustmentType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'project_id',
    'name',
    'description',
    'type',
    'value',
    'reason',
])]
class PricingAdjustment extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'value' => 'decimal:4',
            'type' => AdjustmentType::class,
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
