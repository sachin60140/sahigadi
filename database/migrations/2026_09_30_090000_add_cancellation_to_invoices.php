<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A tax invoice is cancelled, never deleted.
     *
     * The number must stay in the series: deleting a row would let the next
     * invoice reuse its number, and a gap-free consecutive series is exactly
     * what Rule 46 requires. Cancelled invoices therefore keep their row, their
     * number and their snapshot, and are reported as cancelled in GSTR-1.
     */
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (! Schema::hasColumn('invoices', 'status')) {
                $table->string('status', 20)->default('issued')->after('issued_at')->index();
            }
            if (! Schema::hasColumn('invoices', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('status');
            }
            if (! Schema::hasColumn('invoices', 'cancellation_reason')) {
                $table->string('cancellation_reason', 500)->nullable()->after('cancelled_at');
            }
            if (! Schema::hasColumn('invoices', 'cancelled_by')) {
                $table->unsignedBigInteger('cancelled_by')->nullable()->after('cancellation_reason');
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            foreach (['cancelled_by', 'cancellation_reason', 'cancelled_at', 'status'] as $column) {
                if (Schema::hasColumn('invoices', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
