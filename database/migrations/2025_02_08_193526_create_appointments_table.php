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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('expert_id')->nullable();
            $table->longText('specialization')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('individual_name')->nullable();
            $table->double('rating')->nullable()->default(0);
            $table->string('status')->nullable();
            $table->string('expert_name')->nullable();
            $table->string('appointment_date')->nullable();
            $table->time('appointment_time');
            $table->text('expert_link')->nullable();
            $table->text('individual_link')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
