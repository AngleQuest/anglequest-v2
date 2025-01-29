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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('administrator_name');
            $table->string('email');
            $table->string('business_email')->nullable();
            $table->string('address')->nullable();
            $table->text('nda_file')->nullable()->comment('company NDA');
            $table->string('company_logo')->nullable()->comment('company logo');
            $table->string('business_reg_number')->nullable();
            $table->string('business_phone')->nullable();
            $table->string('company_size')->nullable();
            $table->string('website')->nullable();
            $table->text('about')->nullable();
            $table->string('service_type')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
