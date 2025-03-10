<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('expert_growth_plans', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('target_level')->nullable();
            $table->longText('available_days');
            $table->longText('available_times');
            $table->longText('guides')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expert_growth_plans');
    }
};
