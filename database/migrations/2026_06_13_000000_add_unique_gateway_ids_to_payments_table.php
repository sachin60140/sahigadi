<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->unique('razorpay_order_id', 'payments_razorpay_order_unique');
            $table->unique('razorpay_payment_id', 'payments_razorpay_payment_unique');
            $table->unique('phonepe_transaction_id', 'payments_phonepe_transaction_unique');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropUnique('payments_razorpay_order_unique');
            $table->dropUnique('payments_razorpay_payment_unique');
            $table->dropUnique('payments_phonepe_transaction_unique');
        });
    }
};
