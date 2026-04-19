<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dealers', function (Blueprint $table) {
            $table->boolean('gst_verified')->default(false)->after('gst_document_path');
            $table->timestamp('gst_verified_at')->nullable()->after('gst_verified');
        });
    }

    public function down(): void
    {
        Schema::table('dealers', function (Blueprint $table) {
            $table->dropColumn(['gst_verified', 'gst_verified_at']);
        });
    }
};
