<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * GST tax invoices issued for wallet recharges.
     *
     * Buyer and supplier details are SNAPSHOTTED onto each row: a tax invoice is
     * a legal document and must keep showing the details that applied when it
     * was issued, even if the dealer later edits their profile or GSTIN.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            // Gap-free series, reset per Indian financial year (April-March).
            $table->string('invoice_number')->unique();
            $table->string('financial_year', 9);          // e.g. "26-27"
            $table->unsignedInteger('sequence');
            $table->timestamp('issued_at');

            // Who it was issued to (exactly one is set).
            $table->unsignedBigInteger('dealer_id')->nullable()->index();
            $table->unsignedBigInteger('customer_id')->nullable()->index();

            // Source transaction (wallet_transactions or customer_wallet_transactions).
            $table->unsignedBigInteger('wallet_transaction_id')->nullable();
            $table->string('wallet_transaction_type', 20)->nullable(); // dealer|customer
            $table->string('payment_gateway')->nullable();
            $table->string('payment_reference')->nullable();

            // Snapshot: supplier
            $table->string('supplier_name');
            $table->string('supplier_gstin')->nullable();
            $table->text('supplier_address')->nullable();
            $table->string('supplier_state')->nullable();
            $table->string('supplier_state_code', 2)->nullable();

            // Snapshot: buyer
            $table->string('buyer_name');
            $table->string('buyer_company')->nullable();
            $table->string('buyer_gstin')->nullable();
            $table->text('buyer_address')->nullable();
            $table->string('buyer_city')->nullable();
            $table->string('buyer_state')->nullable();
            $table->string('buyer_pincode')->nullable();
            $table->string('buyer_phone')->nullable();
            $table->string('buyer_email')->nullable();

            // Supply details
            $table->string('place_of_supply')->nullable();
            $table->string('place_of_supply_code', 2)->nullable();
            $table->string('sac_code')->nullable();
            $table->string('description');
            $table->boolean('reverse_charge')->default(false);

            // Amounts. Intra-state -> CGST+SGST; inter-state -> IGST.
            $table->decimal('taxable_value', 12, 2);
            $table->decimal('cgst_rate', 5, 2)->default(0);
            $table->decimal('cgst_amount', 12, 2)->default(0);
            $table->decimal('sgst_rate', 5, 2)->default(0);
            $table->decimal('sgst_amount', 12, 2)->default(0);
            $table->decimal('igst_rate', 5, 2)->default(0);
            $table->decimal('igst_amount', 12, 2)->default(0);
            $table->decimal('total_tax', 12, 2);
            $table->decimal('total_amount', 12, 2);

            $table->timestamps();

            // One invoice per source transaction, and a gap-free series per FY.
            $table->unique(['financial_year', 'sequence']);
            $table->unique(['wallet_transaction_type', 'wallet_transaction_id'], 'invoices_txn_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
