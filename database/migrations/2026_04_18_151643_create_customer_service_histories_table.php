<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_service_histories', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('vehicle_number');
            $table->boolean('is_success')->default(false);
            $table->decimal('paid_amount', 10, 2)->nullable();
            $table->string('razorpay_payment_id')->nullable();
            $table->string('razorpay_order_id')->nullable();
            $table->string('error_message')->nullable();
            $table->json('raw_response')->nullable();
            $table->timestamps();
        });

        Schema::create('csh_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_service_history_id', 'csh_id_fk')->constrained('customer_service_histories')->onDelete('cascade');
            $table->string('chassis_no')->nullable();
            $table->string('location_code')->nullable();
            $table->string('location_name')->nullable();
            $table->string('mileage')->nullable();
            $table->decimal('net_bill_amt', 10, 2)->nullable();
            $table->string('online_payment_flag')->nullable();
            $table->decimal('out_standing_amt', 10, 2)->nullable();
            $table->decimal('paid_amt', 10, 2)->nullable();
            $table->string('dealer_code')->nullable();
            $table->string('dealer_name')->nullable();
            $table->date('repair_order_bill_date')->nullable();
            $table->string('repair_order_bill_no')->nullable();
            $table->date('svc_date')->nullable();
            $table->string('repair_order_no')->nullable();
            $table->string('register_no')->nullable();
            $table->string('service_assistant_no')->nullable();
            $table->string('service_assistant_name')->nullable();
            $table->string('work_type')->nullable();
            $table->string('status')->nullable();
            $table->string('service_cate')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('csh_records');
        Schema::dropIfExists('customer_service_histories');
    }
};
