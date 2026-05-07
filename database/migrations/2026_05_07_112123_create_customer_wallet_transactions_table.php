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
        Schema::create('customer_wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_wallet_id')->constrained('customer_wallets')->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->enum('type', ['credit', 'debit']);
            $table->string('remark')->nullable();
            $table->string('reference_id')->nullable();
            $table->string('reference_type')->nullable();
            $table->boolean('is_duplicate_check')->default(false);
            $table->timestamps();

            $table->index(['reference_id', 'reference_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_wallet_transactions');
    }
};
