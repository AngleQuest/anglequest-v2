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
        Schema::create('support_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('expert_id')->nullable();
            $table->longText('specialization')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->text('attachment')->nullable();
            $table->string('prefmode')->nullable();
            $table->string('priority')->nullable();
            $table->string('name')->nullable();
            $table->double('rating')->nullable()->default(0);
            $table->string('task_status')->nullable();
            $table->string('expert_name')->nullable();
            $table->string('deadline')->nullable();
            $table->text('expert_link')->nullable();
            $table->text('candidate_link')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_requests');
    }
};
