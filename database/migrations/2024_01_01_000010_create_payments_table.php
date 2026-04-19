<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dealer_id')->constrained('dealers')->onDelete('cascade');
            $table->string('razorpay_order_id')->nullable();
            $table->string('razorpay_payment_id')->nullable();
            $table->string('razorpay_signature')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('currency')->default('INR');
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->string('type')->comment('wallet_recharge, plan_purchase, featured_listing');
            $table->string('reference_id')->nullable();
            $table->boolean('is_duplicate')->default(false);
            $table->timestamps();

            $table->index(['razorpay_order_id']);
            $table->index(['razorpay_payment_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
