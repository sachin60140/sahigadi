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
        Schema::create('customer_mahindra_service_history_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_mahindra_service_history_id');
            $table->foreign('customer_mahindra_service_history_id', 'cmshr_fk')
                  ->references('id')
                  ->on('customer_mahindra_service_histories')
                  ->onDelete('cascade');
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_mahindra_service_history_records');
    }
};
