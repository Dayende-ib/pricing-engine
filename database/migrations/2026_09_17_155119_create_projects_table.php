<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pricing_profile_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('client_name')->nullable();
            $table->string('client_email')->nullable();
            $table->text('description')->nullable();
            $table->string('project_type')->nullable();
            $table->string('status')->default('draft');
            $table->string('complexity')->default('normal');
            $table->string('risk_level')->default('low');
            $table->date('deadline')->nullable();
            $table->decimal('budget_min', 15, 2)->nullable();
            $table->decimal('budget_max', 15, 2)->nullable();
            $table->string('currency', 3)->default('XOF');
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
