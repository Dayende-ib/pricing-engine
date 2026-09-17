<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pricing_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pricing_profile_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->decimal('rate', 15, 2)->default(0);
            $table->string('unit')->default('hour');
            $table->timestamps();

            $table->index('pricing_profile_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pricing_rates');
    }
};
