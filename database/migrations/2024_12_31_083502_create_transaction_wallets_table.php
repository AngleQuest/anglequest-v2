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
        Schema::create('transaction_wallets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('payment_id')->nullable()->comment('Funding ref ID');
            $table->enum('type', ['credit', 'debit'])->default('debit');
            $table->double('credit', 19,2)->default(0);
            $table->double('debit', 19,2)->default(0);
            $table->string('remark')->nullable();
            $table->enum('status', ['verified', 'unverified'])->nullable()->comment('handles funding');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_wallets');
    }
};
