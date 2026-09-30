<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Marks a listing that was taken down by an account deletion, so restoring the
 * account inside the grace period brings back exactly the listings the deletion
 * hid - and not one the seller had deliberately deactivated themselves before
 * they deleted.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_car_listings', function (Blueprint $table) {
            if (! Schema::hasColumn('customer_car_listings', 'deactivated_by_deletion_at')) {
                $table->timestamp('deactivated_by_deletion_at')->nullable()->after('is_active');
            }
        });
    }

    public function down(): void
    {
        Schema::table('customer_car_listings', function (Blueprint $table) {
            if (Schema::hasColumn('customer_car_listings', 'deactivated_by_deletion_at')) {
                $table->dropColumn('deactivated_by_deletion_at');
            }
        });
    }
};
