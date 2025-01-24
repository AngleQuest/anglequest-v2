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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('type')->comment('to differentiate between individual and business');
            $table->double('number_of_users')->default(0);
            $table->double('price')->default(0);
            $table->enum('duration',['yearly','monthly'])->default('yearly');
            $table->text('note');
            $table->longText('fetures');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
