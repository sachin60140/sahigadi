<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dealers', function (Blueprint $table) {
            $table->string('gst_number', 15)->nullable();
            $table->string('kyc_document_type')->nullable();
            $table->string('kyc_document_number')->nullable();
            $table->string('kyc_document_path')->nullable();
            $table->string('gst_document_path')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('dealers', function (Blueprint $table) {
            $table->dropColumn(['gst_number', 'kyc_document_type', 'kyc_document_number', 'kyc_document_path', 'gst_document_path']);
        });
    }
};
