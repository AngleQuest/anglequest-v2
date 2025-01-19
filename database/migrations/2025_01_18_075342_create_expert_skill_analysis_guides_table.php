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
        Schema::create('expert_skill_analysis_guides', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('Expert incharge');
            $table->string('current_role')->nullable();
            $table->string('level');
            $table->json('available_days');
            $table->string('available_time');
            $table->string('topic');
            $table->json('guides');
            $table->string('location');
            $table->string('time_zone');
            $table->string('status')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expert_skill_analysis_guides');
    }
};
