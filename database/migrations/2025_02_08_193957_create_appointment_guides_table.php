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
        Schema::create('appointment_guides', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('Expert incharge');
            $table->longText('specialization')->nullable();
            $table->json('available_days');
            $table->string('topic');
            $table->json('guides');
            $table->string('location');
            $table->string('time_zone');
            $table->string('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_guides');
    }
};
