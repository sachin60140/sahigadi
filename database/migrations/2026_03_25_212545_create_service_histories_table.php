<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dealer_id')->constrained()->onDelete('cascade');
            $table->string('vehicle_number');
            $table->boolean('is_success')->default(false);
            $table->decimal('debit_amount', 10, 2)->default(0);
            $table->text('error_message')->nullable();
            $table->json('raw_response')->nullable();
            $table->timestamps();

            $table->index('vehicle_number');
            $table->index('dealer_id');
        });

        Schema::create('service_history_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_history_id')->constrained()->onDelete('cascade');
            $table->string('chassis_no')->nullable();
            $table->string('location_code')->nullable();
            $table->string('location_name')->nullable();
            $table->string('mileage')->nullable();
            $table->string('net_bill_amt')->nullable();
            $table->string('online_payment_flag')->nullable();
            $table->string('out_standing_amt')->nullable();
            $table->string('paid_amt')->nullable();
            $table->string('dealer_code')->nullable();
            $table->string('dealer_name')->nullable();
            $table->string('repair_order_bill_date')->nullable();
            $table->string('repair_order_bill_no')->nullable();
            $table->string('svc_date')->nullable();
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
        Schema::dropIfExists('service_history_records');
        Schema::dropIfExists('service_histories');
    }
};
