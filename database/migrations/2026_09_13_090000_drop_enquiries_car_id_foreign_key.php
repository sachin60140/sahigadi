<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * enquiries.car_id is polymorphic in practice: it points at cars when
     * dealer_id is set, and at customer_car_listings when it is null. That is
     * how Enquiry::getActualCarAttribute and the customer enquiry list read it.
     *
     * A foreign key to cars.id made that impossible, so every enquiry on a
     * customer listing failed with a constraint violation. The mobile
     * "Enquire Now" button and the web contact-unlock flow both hit it.
     *
     * The cascade the constraint provided is replaced by explicit cleanup in
     * the Car and CustomerCarListing models.
     */
    public function up(): void
    {
        if ($this->constraintExists('enquiries_car_id_foreign')) {
            Schema::table('enquiries', function ($table) {
                $table->dropForeign('enquiries_car_id_foreign');
            });
        }

        // Keep the column indexed: the constraint was providing the index.
        if (! $this->indexExists('enquiries', 'enquiries_car_id_index')) {
            Schema::table('enquiries', function ($table) {
                $table->index('car_id', 'enquiries_car_id_index');
            });
        }
    }

    public function down(): void
    {
        // Deliberately not restoring the foreign key: it cannot coexist with
        // enquiries on customer listings. Only drop the index we added.
        if ($this->indexExists('enquiries', 'enquiries_car_id_index')) {
            Schema::table('enquiries', function ($table) {
                $table->dropIndex('enquiries_car_id_index');
            });
        }
    }

    private function constraintExists(string $name): bool
    {
        return DB::table('information_schema.TABLE_CONSTRAINTS')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', 'enquiries')
            ->where('CONSTRAINT_NAME', $name)
            ->exists();
    }

    private function indexExists(string $table, string $index): bool
    {
        return DB::table('information_schema.STATISTICS')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', $table)
            ->where('INDEX_NAME', $index)
            ->exists();
    }
};
