<?php

namespace App\Models;

use App\Enums\AIAnalysisStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'project_id',
    'provider',
    'model',
    'prompt_version',
    'input',
    'output',
    'status',
    'confidence',
])]
class AIAnalysis extends Model
{
    use HasFactory;

    protected $table = 'ai_analyses';

    protected function casts(): array
    {
        return [
            'confidence' => 'decimal:2',
            'status' => AIAnalysisStatus::class,
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
