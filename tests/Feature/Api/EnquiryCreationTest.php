<?php

namespace Tests\Feature\Api;

use App\Models\Enquiry;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Car;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * The mobile enquiry endpoint wrote to columns that do not exist
 * (is_customer_listing, customer_id, phone, name), so every "Enquire Now" tap
 * failed with a SQL error. These lock the real column contract in place.
 */
class EnquiryCreationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_the_enquiries_table_has_the_columns_the_api_writes(): void
    {
        $columns = Schema::getColumnListing('enquiries');

        foreach ([
            'car_id',
            'dealer_id',
            'customer_name',
            'customer_phone',
            'customer_email',
            'message',
            'status',
            'ip_address',
        ] as $column) {
            $this->assertContains($column, $columns, "enquiries.$column is missing");
        }
    }

    public function test_a_dealer_car_enquiry_records_the_dealer(): void
    {
        $enquiry = Enquiry::create([
            'car_id' => 1,
            'dealer_id' => 1,
            'customer_name' => 'Buyer',
            'customer_phone' => '9999999999',
            'status' => 'new',
        ]);

        $this->assertNotNull($enquiry->id);
        $this->assertSame(1, $enquiry->dealer_id);
    }

    public function test_car_id_has_no_foreign_key_to_cars(): void
    {
        // car_id points at cars or customer_car_listings depending on
        // dealer_id, so a constraint to cars.id rejected every enquiry on a
        // customer listing. Production enforced it; that is what broke
        // "Enquire Now".
        $constraint = DB::table('information_schema.TABLE_CONSTRAINTS')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', 'enquiries')
            ->where('CONSTRAINT_NAME', 'enquiries_car_id_foreign')
            ->exists();

        $this->assertFalse($constraint, 'enquiries.car_id must not be constrained to cars');
    }

    public function test_an_enquiry_can_reference_a_listing_that_is_not_a_car(): void
    {
        $carIds = Car::pluck('id')->all();
        $unusedId = (Car::max('id') ?? 0) + 100000;

        $this->assertNotContains($unusedId, $carIds);

        $enquiry = Enquiry::create([
            'car_id' => $unusedId,
            'dealer_id' => null,
            'customer_name' => 'Buyer',
            'customer_phone' => '9999999999',
            'status' => 'new',
        ]);

        $this->assertNotNull($enquiry->id);
    }

    public function test_a_customer_listing_enquiry_leaves_the_dealer_null(): void
    {
        // A null dealer_id is how the app marks a customer listing. The owner's
        // enquiry list and Enquiry::getActualCarAttribute both depend on it.
        $enquiry = Enquiry::create([
            'car_id' => 1,
            'dealer_id' => null,
            'customer_name' => 'Buyer',
            'customer_phone' => '9999999999',
            'status' => 'new',
        ]);

        $this->assertNull($enquiry->dealer_id);

        $visible = Enquiry::whereNull('dealer_id')
            ->whereIn('car_id', [1])
            ->where('id', $enquiry->id)
            ->exists();

        $this->assertTrue($visible, 'the listing owner must be able to see it');
    }
}
