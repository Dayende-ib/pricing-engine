<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('quote_number')->unique();
            $table->string('status')->default('draft');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('adjustments_total', 15, 2)->default(0);
            $table->decimal('external_costs_total', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->string('currency', 3)->default('XOF');
            $table->date('valid_until')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['project_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
