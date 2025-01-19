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
        Schema::create('expert_skill_analysis_feedback', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('expert_id');
            $table->unsignedBigInteger('skill_analysis_id');
            $table->string('remark')->nullable();
            $table->string('rating')->nullable();
            $table->string('descriptions')->nullable();
            $table->string('role')->nullable();
            $table->string('types')->nullable();
            $table->string('starting_level')->nullable();
            $table->string('target_level')->nullable();
            $table->string('date')->nullable();
            $table->string('completed')->nullable();
            $table->string('rating_figure')->nullable();
            $table->string('expert_analysis')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expert_skill_analysis_feedback');
    }
};
