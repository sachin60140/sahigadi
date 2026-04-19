<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_car_listings', function (Blueprint $table) {
            $table->string('whatsapp_number', 20)->nullable()->after('owner_phone');
            $table->text('images')->nullable()->after('rejection_reason');
        });
    }

    public function down(): void
    {
        Schema::table('customer_car_listings', function (Blueprint $table) {
            $table->dropColumn(['whatsapp_number', 'images']);
        });
    }
};
