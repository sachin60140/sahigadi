<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maruti_service_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dealer_id')->nullable()->constrained('dealers')->onDelete('set null');
            $table->string('vehicle_number', 20);
            $table->boolean('is_success')->default(false);
            $table->decimal('debit_amount', 10, 2)->nullable();
            $table->text('error_message')->nullable();
            $table->json('raw_response')->nullable();
            $table->timestamps();

            $table->index(['dealer_id', 'vehicle_number']);
            $table->index('vehicle_number');
        });

        Schema::create('maruti_service_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('maruti_service_history_id');
            $table->foreign('maruti_service_history_id', 'msr_fk')->references('id')->on('maruti_service_histories')->onDelete('cascade');
            $table->string('chassis_no')->nullable();
            $table->string('location_code')->nullable();
            $table->string('dealer_code')->nullable();
            $table->string('dealer_name')->nullable();
            $table->text('dealer_address')->nullable();
            $table->string('mileage')->nullable();
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->decimal('part_amount', 10, 2)->nullable();
            $table->decimal('labour_amount', 10, 2)->nullable();
            $table->date('repair_order_bill_date')->nullable();
            $table->string('repair_order_bill_no')->nullable();
            $table->date('svc_date')->nullable();
            $table->string('repair_order_no')->nullable();
            $table->string('register_no')->nullable();
            $table->string('service_assistant_name')->nullable();
            $table->string('work_type')->nullable();
            $table->string('status')->nullable();
            $table->string('service_cate')->nullable();
            $table->timestamps();
        });

        Schema::create('cust_maruti_services', function (Blueprint $table) {
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

        Schema::create('cust_maruti_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cust_maruti_service_id');
            $table->foreign('cust_maruti_service_id', 'cmsr_fk')->references('id')->on('cust_maruti_services')->onDelete('cascade');
            $table->string('chassis_no')->nullable();
            $table->string('location_code')->nullable();
            $table->string('dealer_code')->nullable();
            $table->string('dealer_name')->nullable();
            $table->text('dealer_address')->nullable();
            $table->string('mileage')->nullable();
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->decimal('part_amount', 10, 2)->nullable();
            $table->decimal('labour_amount', 10, 2)->nullable();
            $table->date('repair_order_bill_date')->nullable();
            $table->string('repair_order_bill_no')->nullable();
            $table->date('svc_date')->nullable();
            $table->string('repair_order_no')->nullable();
            $table->string('register_no')->nullable();
            $table->string('service_assistant_name')->nullable();
            $table->string('work_type')->nullable();
            $table->string('status')->nullable();
            $table->string('service_cate')->nullable();
            $table->timestamps();
        });

        Schema::create('admin_maruti_service_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dealer_id')->nullable()->constrained('dealers')->onDelete('set null');
            $table->string('vehicle_number', 20);
            $table->integer('service_count')->default(0);
            $table->decimal('charge_amount', 10, 2)->nullable();
            $table->boolean('is_success')->default(false);
            $table->text('error_message')->nullable();
            $table->json('raw_response')->nullable();
            $table->timestamps();

            $table->index(['dealer_id', 'vehicle_number']);
            $table->index('vehicle_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_maruti_service_histories');
        Schema::dropIfExists('cust_maruti_records');
        Schema::dropIfExists('cust_maruti_services');
        Schema::dropIfExists('maruti_service_records');
        Schema::dropIfExists('maruti_service_histories');
    }
};
