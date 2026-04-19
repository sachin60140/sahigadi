<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dealer_id')->constrained()->onDelete('cascade');
            $table->string('registration_number');
            $table->string('owner_name')->nullable();
            $table->string('father_name')->nullable();
            $table->text('address')->nullable();
            $table->string('vehicle_class')->nullable();
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->string('variant')->nullable();
            $table->string('color')->nullable();
            $table->string('fuel_type')->nullable();
            $table->string('engine_number')->nullable();
            $table->string('chassis_number')->nullable();
            $table->date('registration_date')->nullable();
            $table->date('fitness_date')->nullable();
            $table->date('insurance_date')->nullable();
            $table->string('insurance_policy_number')->nullable();
            $table->string('insurance_provider')->nullable();
            $table->string('puc_number')->nullable();
            $table->date('puc_validity')->nullable();
            $table->string('tax_amount')->nullable();
            $table->date('tax_validity')->nullable();
            $table->integer('seats')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('vehicle_category')->nullable();
            $table->string('rc_status')->nullable();
            $table->string('blacklist_status')->nullable();
            $table->boolean('financed')->default(false);
            $table->string('lender_name')->nullable();
            $table->string('rto_location')->nullable();
            $table->string('norms_type')->nullable();
            $table->string('cubic_capacity')->nullable();
            $table->string('unladen_weight')->nullable();
            $table->string('gross_weight')->nullable();
            $table->integer('cylinders')->nullable();
            $table->boolean('is_commercial')->default(false);
            $table->string('permit_number')->nullable();
            $table->string('permit_type')->nullable();
            $table->date('permit_validity')->nullable();
            $table->string('manufactured_date')->nullable();
            $table->decimal('debit_amount', 10, 2)->default(0);
            $table->json('raw_response')->nullable();
            $table->boolean('is_success')->default(false);
            $table->string('error_message')->nullable();
            $table->string('api_provider')->nullable();
            $table->timestamps();

            $table->index('registration_number');
            $table->index('dealer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_details');
    }
};
