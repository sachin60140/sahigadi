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
        $tables = [
            'cust_maruti_services',
            'customer_service_histories',
            'customer_challan_searches',
            'customer_vehicle_searches'
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table_bp) use ($table) {
                if (!Schema::hasColumn($table, 'is_refunded')) {
                    $table_bp->boolean('is_refunded')->default(false)->after('is_success');
                }
                if (!Schema::hasColumn($table, 'razorpay_refund_id')) {
                    $table_bp->string('razorpay_refund_id')->nullable()->after('razorpay_payment_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_service_tables', function (Blueprint $table) {
            //
        });
    }
};
