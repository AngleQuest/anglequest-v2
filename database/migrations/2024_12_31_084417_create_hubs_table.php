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
        Schema::create('hubs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('visibility');
            $table->string('name');
            $table->string('hub_description');
            $table->string('meeting_day');
            $table->string('from');
            $table->string('to');
            $table->string('coaching_hub_fee');
            $table->string('coaching_hub_goals');
            $table->string('coaching_hub_limit');
            $table->string('category');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hubs');
    }
};
