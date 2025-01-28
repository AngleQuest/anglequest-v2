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
        Schema::create('user_slas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('User applying to join');
            $table->unsignedBigInteger('sla_id')->comment('Sla Id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_slas');
    }
};
