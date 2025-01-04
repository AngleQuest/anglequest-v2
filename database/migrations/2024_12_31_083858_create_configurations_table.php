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
        Schema::create('configurations', function (Blueprint $table) {
            $table->id();
            $table->double('usd_rate')->default(0)->comment('Local currency rate per USD');
            $table->enum('email_verify', ['enabled', 'disabled'])->nullable();
            $table->string('currency_code')->nullable()->comment('eg: USD');
            $table->string('currency_symbol')->nullable()->comment('eg: $');
            $table->integer('paystack_perc')->default(0)->comment('Paystack charges percentage');
            $table->double('paystack_fixed')->default(0)->comment('Paystack fixed prize for charges');
            $table->double('paystack_max')->default(0)->comment('Paystack maximum charge per transaction');
            $table->double('withdrawal_min')->default(0)->comment('minimum amount to have in wallet before withdrawal');
            $table->double('withdrawal_max')->default(0)->comment('maximum amount to withdraw from wallet ');
            $table->double('expert_fee')->default(0)->comment('Expert Meeting charges');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configurations');
    }
};
