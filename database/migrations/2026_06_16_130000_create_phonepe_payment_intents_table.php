<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Persists the dealer PhonePe payment intent (type/plan/car/days) keyed by
     * merchant order id, so the server-to-server webhook can fulfil the order
     * correctly even when the browser redirect callback never runs.
     */
    public function up(): void
    {
        Schema::create('phonepe_payment_intents', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique();
            $table->unsignedBigInteger('dealer_id')->nullable();
            $table->string('type');
            $table->unsignedBigInteger('plan_id')->nullable();
            $table->unsignedBigInteger('car_id')->nullable();
            $table->unsignedSmallInteger('days')->nullable();
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('phonepe_payment_intents');
    }
};
