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
        Schema::table('customer_service_histories', function (Blueprint $table) {
            $table->longText('error_message')->nullable()->change();
        });
        Schema::table('customer_vehicle_searches', function (Blueprint $table) {
            $table->longText('error_message')->nullable()->change();
        });
        Schema::table('customer_challan_searches', function (Blueprint $table) {
            $table->longText('error_message')->nullable()->change();
        });
        Schema::table('admin_challan_searches', function (Blueprint $table) {
            $table->longText('error_message')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_service_histories', function (Blueprint $table) {
            $table->string('error_message')->nullable()->change();
        });
        Schema::table('customer_vehicle_searches', function (Blueprint $table) {
            $table->string('error_message')->nullable()->change();
        });
        Schema::table('customer_challan_searches', function (Blueprint $table) {
            $table->string('error_message')->nullable()->change();
        });
        Schema::table('admin_challan_searches', function (Blueprint $table) {
            $table->string('error_message')->nullable()->change();
        });
    }
};
