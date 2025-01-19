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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_id')->nullable()->comment('used to track our services');
            $table->string('title');
            $table->string('cost');
            $table->integer('country_id')->nullable();
            $table->enum('period', ['monthly', 'yearly']);
            $table->json('tagline')->nullable();
            $table->longText('details')->nullable();
            $table->enum('status', ['active', 'inactive']);
            $table->enum('type', ['individual', 'company'])->default('individual')->comment('Differentiate between an Individual and company plans');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
