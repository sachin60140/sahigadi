<?php

namespace Tests\Feature\Api;

use App\Models\Enquiry;
use Illuminate\Foundation\Testing\DatabaseTransactions;
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
