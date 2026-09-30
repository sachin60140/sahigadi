<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Account deletion, in two stages.
 *
 * Google Play requires any app that creates accounts to offer an in-app route
 * to delete one and a public web URL for the same. Deleting the row outright is
 * not an option here: invoices are GST tax invoices in a gap-free consecutive
 * series and carry customer_id, and payments records back real money.
 *
 * So deletion soft-deletes immediately (the account vanishes from every surface
 * and the tokens are revoked), and a scheduled pass anonymises for good once the
 * grace period has run. Signing in with the same number inside the window
 * restores the account, which matters because OTP login means a deletion can be
 * triggered by anyone holding the handset.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (! Schema::hasColumn('customers', 'deleted_at')) {
                $table->softDeletes()->index();
            }
            if (! Schema::hasColumn('customers', 'anonymised_at')) {
                // Set when the grace period expires and the personal fields are
                // scrambled. A row with this set can never be restored.
                $table->timestamp('anonymised_at')->nullable()->after('deleted_at');
            }
        });

        // Only created when a deleted account still held money. The phone is
        // kept deliberately and only for as long as it takes to return the
        // balance, because there is no way for a customer to withdraw it
        // themselves. Both facts are disclosed on the public deletion page.
        if (! Schema::hasTable('account_deletion_refunds')) {
            Schema::create('account_deletion_refunds', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('customer_id')->index();
                $table->string('contact_phone', 20);
                $table->string('contact_name')->nullable();
                $table->decimal('balance', 12, 2);
                $table->string('status', 20)->default('pending')->index();
                $table->timestamp('requested_at');
                $table->timestamp('refunded_at')->nullable();
                $table->string('reference')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('account_deletion_refunds');

        Schema::table('customers', function (Blueprint $table) {
            foreach (['anonymised_at', 'deleted_at'] as $column) {
                if (Schema::hasColumn('customers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
