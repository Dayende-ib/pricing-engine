<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'pricing_profile_id',
    'name',
    'rate',
    'unit',
])]
class PricingRate extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'rate' => 'decimal:2',
        ];
    }

    public function pricingProfile(): BelongsTo
    {
        return $this->belongsTo(PricingProfile::class);
    }
}
