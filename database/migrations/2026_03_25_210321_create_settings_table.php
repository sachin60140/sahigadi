<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string');
            $table->string('group')->default('general');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('admin_vehicle_searches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dealer_id')->constrained()->onDelete('cascade');
            $table->string('registration_number');
            $table->string('owner_name')->nullable();
            $table->text('address')->nullable();
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->string('fuel_type')->nullable();
            $table->string('registration_date')->nullable();
            $table->string('rc_status')->nullable();
            $table->string('insurance_date')->nullable();
            $table->string('insurance_policy_number')->nullable();
            $table->string('puc_validity')->nullable();
            $table->string('chassis_number')->nullable();
            $table->string('engine_number')->nullable();
            $table->decimal('charge_amount', 10, 2)->default(0);
            $table->boolean('is_success')->default(false);
            $table->text('error_message')->nullable();
            $table->json('raw_response')->nullable();
            $table->timestamps();

            $table->index('registration_number');
            $table->index('dealer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_vehicle_searches');
        Schema::dropIfExists('settings');
    }
};
