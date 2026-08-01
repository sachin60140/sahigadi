<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * API access is now granted to every dealer by default. Access is still
     * gated by the global master switch, dealer approval status and wallet
     * balance, and admins can still disable an individual dealer.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('dealers', 'api_enabled')) {
            return;
        }

        // Change the column default for newly created dealers.
        DB::statement('ALTER TABLE `dealers` MODIFY `api_enabled` TINYINT(1) NOT NULL DEFAULT 1');

        // Backfill existing dealers.
        DB::table('dealers')->where('api_enabled', false)->update(['api_enabled' => true]);
    }

    public function down(): void
    {
        if (! Schema::hasColumn('dealers', 'api_enabled')) {
            return;
        }

        DB::statement('ALTER TABLE `dealers` MODIFY `api_enabled` TINYINT(1) NOT NULL DEFAULT 0');
    }
};
