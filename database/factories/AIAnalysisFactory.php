<?php

namespace Database\Factories;

use App\Enums\AIAnalysisStatus;
use App\Models\AIAnalysis;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AIAnalysis>
 */
class AIAnalysisFactory extends Factory
{
    protected $model = AIAnalysis::class;

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'provider' => 'OpenRouter',
            'model' => fake()->randomElement(['anthropic/claude-3.5-sonnet', 'openai/gpt-4o', 'google/gemini-pro']),
            'prompt_version' => 'v1.0',
            'input' => fake()->paragraphs(3, true),
            'output' => json_encode([
                'project_type' => 'web_app',
                'complexity' => 'normal',
                'features' => [],
                'risks' => [],
                'ambiguities' => [],
                'questions' => [],
            ]),
            'status' => fake()->randomElement(AIAnalysisStatus::cases()),
            'confidence' => fake()->randomFloat(2, 0.5, 0.95),
        ];
    }
}
