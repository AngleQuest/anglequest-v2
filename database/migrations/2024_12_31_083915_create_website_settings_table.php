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
        Schema::create('website_settings', function (Blueprint $table) {
            $table->id();
            $table->string('logo_url')->nullable();
            $table->string('dashboard_logo_url')->nullable();
            $table->text('site_description')->nullable();
            $table->text('vision')->nullable();
            $table->text('mission')->nullable();
            $table->text('motto')->nullable();
            $table->string('favicon_url')->nullable();
            $table->string('name')->default('Site Name');
            $table->string('business_name')->nullable();
            $table->string('business_address')->nullable();
            $table->string('business_phone')->nullable();
            $table->string('business_email')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_settings');
    }
};
