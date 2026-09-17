<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('provider')->nullable();
            $table->string('model')->nullable();
            $table->string('prompt_version')->nullable();
            $table->longText('input')->nullable();
            $table->longText('output')->nullable();
            $table->string('status')->default('pending');
            $table->decimal('confidence', 3, 2)->nullable();
            $table->timestamps();

            $table->index(['project_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_analyses');
    }
};
