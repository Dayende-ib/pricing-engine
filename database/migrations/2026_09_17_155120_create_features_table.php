<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('estimated_hours', 8, 2)->default(0);
            $table->string('complexity')->default('normal');
            $table->string('priority')->default('normal');
            $table->string('estimation_source')->default('human');
            $table->timestamps();

            $table->index(['project_id', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('features');
    }
};
