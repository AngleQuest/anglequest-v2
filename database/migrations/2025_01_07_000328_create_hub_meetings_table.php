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
        Schema::create('hub_meetings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('member of the hub');
            $table->unsignedBigInteger('expert_id')->comment('Expert incharge');
            $table->unsignedBigInteger('hub_id')->comment('The hub that host the meeting');
            $table->string('meeting_topic')->nullable();
            $table->longText('description')->nullable();
            $table->dateTime('meeting_date')->nullable();
            $table->string('meeting_time')->nullable();
            $table->longText('candidate_link')->nullable();
            $table->longText('expert_link')->nullable();
            $table->enum('status',['active','past','expired','coming'])->define('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hub_meetings');
    }
};
