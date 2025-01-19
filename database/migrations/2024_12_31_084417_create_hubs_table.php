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
            $table->unsignedBigInteger('user_id')->comment('Expert incharge');
            $table->string('visibility');
            $table->string('name');
            $table->json('specialization');
            $table->longText('description');
            $table->string('meeting_day')->nullable();
            $table->string('meeting_time')->nullable();
            $table->string('from')->nullable();
            $table->string('to')->nullable();
            $table->longText('hub_goals');
            $table->double('hub_limit')->default(20)->comment('decrements in process or member registration');
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
