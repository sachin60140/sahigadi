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
        Schema::table('customers', function (Blueprint $table) {
            $table->string('aadhaar_number')->nullable()->after('pincode');
            $table->string('pan_number')->nullable()->after('aadhaar_number');
            $table->string('gender')->nullable()->after('pan_number');
            $table->date('dob')->nullable()->after('gender');
            $table->integer('profile_completion_percentage')->default(0)->after('whatsapp_number');
            $table->timestamp('profile_completed_at')->nullable()->after('profile_completion_percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'aadhaar_number',
                'pan_number',
                'gender',
                'dob',
                'profile_completion_percentage',
                'profile_completed_at'
            ]);
        });
    }
};
