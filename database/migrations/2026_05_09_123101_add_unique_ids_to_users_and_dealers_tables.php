<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('customer_unique_id')->nullable()->unique()->after('id');
        });

        Schema::table('dealers', function (Blueprint $table) {
            $table->string('dealer_unique_id')->nullable()->unique()->after('id');
        });

        // Populate existing customers
        DB::table('customers')->orderBy('id')->chunk(100, function ($customers) {
            foreach ($customers as $customer) {
                DB::table('customers')
                    ->where('id', $customer->id)
                    ->update(['customer_unique_id' => 'CUS' . (10000 + $customer->id)]);
            }
        });

        // Populate existing dealers
        DB::table('dealers')->orderBy('id')->chunk(100, function ($dealers) {
            foreach ($dealers as $dealer) {
                DB::table('dealers')
                    ->where('id', $dealer->id)
                    ->update(['dealer_unique_id' => 'DLR' . (10000 + $dealer->id)]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('customer_unique_id');
        });

        Schema::table('dealers', function (Blueprint $table) {
            $table->dropColumn('dealer_unique_id');
        });
    }
};
