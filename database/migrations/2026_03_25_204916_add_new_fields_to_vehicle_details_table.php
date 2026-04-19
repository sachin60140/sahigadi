<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicle_details', function (Blueprint $table) {
            if (! Schema::hasColumn('vehicle_details', 'insurance_provider')) {
                $table->string('insurance_provider')->nullable()->after('insurance_policy_number');
            }
            if (! Schema::hasColumn('vehicle_details', 'financed')) {
                $table->boolean('financed')->default(false)->after('blacklist_status');
            }
            if (! Schema::hasColumn('vehicle_details', 'lender_name')) {
                $table->string('lender_name')->nullable()->after('financed');
            }
            if (! Schema::hasColumn('vehicle_details', 'rto_location')) {
                $table->string('rto_location')->nullable()->after('lender_name');
            }
            if (! Schema::hasColumn('vehicle_details', 'norms_type')) {
                $table->string('norms_type')->nullable()->after('rto_location');
            }
            if (! Schema::hasColumn('vehicle_details', 'cubic_capacity')) {
                $table->string('cubic_capacity')->nullable()->after('norms_type');
            }
            if (! Schema::hasColumn('vehicle_details', 'unladen_weight')) {
                $table->string('unladen_weight')->nullable()->after('cubic_capacity');
            }
            if (! Schema::hasColumn('vehicle_details', 'gross_weight')) {
                $table->string('gross_weight')->nullable()->after('unladen_weight');
            }
            if (! Schema::hasColumn('vehicle_details', 'cylinders')) {
                $table->integer('cylinders')->nullable()->after('gross_weight');
            }
            if (! Schema::hasColumn('vehicle_details', 'is_commercial')) {
                $table->boolean('is_commercial')->default(false)->after('cylinders');
            }
            if (! Schema::hasColumn('vehicle_details', 'permit_number')) {
                $table->string('permit_number')->nullable()->after('is_commercial');
            }
            if (! Schema::hasColumn('vehicle_details', 'permit_type')) {
                $table->string('permit_type')->nullable()->after('permit_number');
            }
            if (! Schema::hasColumn('vehicle_details', 'permit_validity')) {
                $table->date('permit_validity')->nullable()->after('permit_type');
            }
            if (! Schema::hasColumn('vehicle_details', 'manufactured_date')) {
                $table->string('manufactured_date')->nullable()->after('permit_validity');
            }
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_details', function (Blueprint $table) {
            $columns = [
                'insurance_provider', 'financed', 'lender_name', 'rto_location',
                'norms_type', 'cubic_capacity', 'unladen_weight', 'gross_weight',
                'cylinders', 'is_commercial', 'permit_number', 'permit_type',
                'permit_validity', 'manufactured_date',
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('vehicle_details', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
