<?php

namespace App\Models;

use App\Enums\QuoteStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'project_id',
    'quote_number',
    'status',
    'subtotal',
    'adjustments_total',
    'external_costs_total',
    'discount',
    'total',
    'currency',
    'valid_until',
    'notes',
])]
class Quote extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'adjustments_total' => 'decimal:2',
            'external_costs_total' => 'decimal:2',
            'discount' => 'decimal:2',
            'total' => 'decimal:2',
            'valid_until' => 'date',
            'status' => QuoteStatus::class,
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuoteItem::class);
    }
}
