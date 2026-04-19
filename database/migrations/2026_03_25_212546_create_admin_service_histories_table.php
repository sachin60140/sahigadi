<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_service_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dealer_id')->constrained()->onDelete('cascade');
            $table->string('vehicle_number');
            $table->integer('service_count')->default(0);
            $table->decimal('charge_amount', 10, 2)->default(0);
            $table->boolean('is_success')->default(false);
            $table->text('error_message')->nullable();
            $table->json('raw_response')->nullable();
            $table->timestamps();

            $table->index('vehicle_number');
            $table->index('dealer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_service_histories');
    }
};
