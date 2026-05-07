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
        Schema::create('customer_mahindra_service_histories', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('vehicle_number', 20);
            $table->boolean('is_success')->default(false);
            $table->decimal('paid_amount', 10, 2)->nullable();
            $table->string('razorpay_payment_id')->nullable();
            $table->string('razorpay_order_id')->nullable();
            $table->text('error_message')->nullable();
            $table->json('raw_response')->nullable();
            $table->timestamps();

            $table->index('vehicle_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_mahindra_service_histories');
    }
};
