<?php

namespace Tests\Feature\Api;

use App\Models\Brand;
use App\Models\Customer;
use App\Models\CustomerCarListing;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * POST /api/actions/customer-listing assigned a PHP array straight to
 * customer_car_listings.images, which is a TEXT column with no array cast on
 * the model. Eloquent handed PDO an array to bind and the insert died, so the
 * whole "Sell Your Car" flow returned 500 on every submission -- after the
 * user had already uploaded at least five photos, which stayed on disk as
 * orphans.
 *
 * The column holds a JSON string: Admin\CustomerCarListingController writes it
 * with json_encode and the sitemap view reads it with json_decode. These lock
 * that contract so the API cannot drift from it again.
 */
class CustomerListingImagesTest extends TestCase
{
    use DatabaseTransactions;

    public function test_images_is_a_text_column_with_no_array_cast(): void
    {
        $this->assertContains('images', Schema::getColumnListing('customer_car_listings'));

        // If an array cast is ever added, the json_encode call sites would
        // double-encode. That is a deliberate fork in the road, not a silent one.
        $this->assertArrayNotHasKey(
            'images',
            (new CustomerCarListing)->getCasts(),
            'An images cast was added; the json_encode call sites must be removed at the same time.'
        );
    }

    public function test_a_customer_can_submit_a_listing_with_photos(): void
    {
        Storage::fake('public');

        $customer = Customer::query()->whereNotNull('phone')->first();
        $brandId = Brand::query()->value('id');

        if (! $customer || ! $brandId) {
            $this->markTestSkipped('Needs a customer with a phone and at least one brand.');
        }

        Sanctum::actingAs($customer, ['role:customer']);

        $response = $this->postJson('/api/actions/customer-listing', $this->payload() + [
            'images' => [
                UploadedFile::fake()->image('front.jpg'),
                UploadedFile::fake()->image('rear.jpg'),
            ],
        ]);

        $response->assertOk()->assertJson(['success' => true]);

        $listing = CustomerCarListing::query()->latest('id')->first();

        $this->assertIsString($listing->images, 'images must reach the column as a JSON string.');

        $paths = json_decode($listing->images, true);
        $this->assertIsArray($paths);
        $this->assertCount(2, $paths);

        foreach ($paths as $path) {
            Storage::disk('public')->assertExists($path);
        }
    }

    public function test_a_listing_submitted_without_photos_still_saves(): void
    {
        Storage::fake('public');

        $customer = Customer::query()->whereNotNull('phone')->first();
        $brandId = Brand::query()->value('id');

        if (! $customer || ! $brandId) {
            $this->markTestSkipped('Needs a customer with a phone and at least one brand.');
        }

        Sanctum::actingAs($customer, ['role:customer']);

        // The original bug failed on the empty case too, so a listing created
        // without uploads took the same 500.
        $this->postJson('/api/actions/customer-listing', $this->payload())
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertSame([], json_decode(CustomerCarListing::query()->latest('id')->value('images'), true));
    }

    private function payload(): array
    {
        return [
            'title' => 'Images contract listing',
            'brand_id' => Brand::query()->value('id'),
            'model' => 'Swift',
            'year' => 2019,
            'fuel_type' => 'petrol',
            'transmission' => 'manual',
            'km_driven' => 42000,
            'price' => 250000,
            'city' => 'Ghaziabad',
            'owners' => 1,
            'latitude' => 28.6692,
            'longitude' => 77.4538,
        ];
    }
}
