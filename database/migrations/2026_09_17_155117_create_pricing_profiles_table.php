<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pricing_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('currency', 3)->default('XOF');
            $table->decimal('hourly_rate', 15, 2)->default(0);
            $table->decimal('minimum_margin', 5, 4)->default(0.20);
            $table->decimal('target_margin', 5, 4)->default(0.35);
            $table->decimal('premium_margin', 5, 4)->default(0.50);
            $table->decimal('default_risk_reserve', 5, 4)->default(0.10);
            $table->decimal('default_revision_hours', 8, 2)->default(0);
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'is_default']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pricing_profiles');
    }
};
