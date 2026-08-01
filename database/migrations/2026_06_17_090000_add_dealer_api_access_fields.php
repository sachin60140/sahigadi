<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('dealers') && ! Schema::hasColumn('dealers', 'api_enabled')) {
            Schema::table('dealers', function (Blueprint $table) {
                $table->boolean('api_enabled')->default(false)->after('status');
            });
        }

        if (Schema::hasTable('admin_vehicle_searches')) {
            Schema::table('admin_vehicle_searches', function (Blueprint $table) {
                if (! Schema::hasColumn('admin_vehicle_searches', 'channel')) {
                    $table->string('channel')->default('web')->after('dealer_id');
                }
                if (! Schema::hasColumn('admin_vehicle_searches', 'ip_address')) {
                    $table->string('ip_address', 45)->nullable()->after('channel');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('dealers', 'api_enabled')) {
            Schema::table('dealers', fn (Blueprint $table) => $table->dropColumn('api_enabled'));
        }

        if (Schema::hasTable('admin_vehicle_searches')) {
            Schema::table('admin_vehicle_searches', function (Blueprint $table) {
                foreach (['channel', 'ip_address'] as $column) {
                    if (Schema::hasColumn('admin_vehicle_searches', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
