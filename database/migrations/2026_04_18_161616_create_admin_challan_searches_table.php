<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_challan_searches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dealer_id')->constrained()->onDelete('cascade');
            $table->string('vehicle_number');
            $table->boolean('is_success')->default(false);
            $table->decimal('charge_amount', 10, 2)->nullable();
            $table->json('challan_data')->nullable();
            $table->decimal('total_amount', 10, 2)->nullable()->default(0);
            $table->integer('challan_count')->nullable()->default(0);
            $table->string('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_challan_searches');
    }
};
