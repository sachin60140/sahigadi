<?php

namespace Tests\Feature\Api;

use App\Models\Car;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * GET /api/public/cars/{slug} eager-loaded 'wallet_balance' on the dealer, a
 * column that does not exist (the balance lives on the wallets table and is
 * reached through Dealer::wallet()). MySQL answered with "Unknown column
 * 'wallet_balance' in 'field list'", so every dealer car in the mobile app
 * returned 500 and its detail screen -- and the Enquire Now button on it --
 * was unreachable.
 *
 * The same select also returned the dealer's phone number on a route with no
 * auth middleware, while the website puts dealer contact details behind an OTP
 * unlock.
 */
class PublicCarDetailTest extends TestCase
{
    use DatabaseTransactions;

    public function test_dealers_has_no_wallet_balance_column(): void
    {
        // If someone ever adds this column the guard below stops meaning
        // anything, so assert the premise rather than silently passing.
        $this->assertNotContains(
            'wallet_balance',
            Schema::getColumnListing('dealers'),
            'dealers.wallet_balance now exists; revisit why carDetail avoided it.'
        );
    }

    public function test_a_dealer_car_detail_responds_without_a_sql_error(): void
    {
        $car = Car::query()
            ->approved()
            ->active()
            ->whereNotNull('slug')
            ->whereNotNull('dealer_id')
            ->first();

        if (! $car) {
            $this->markTestSkipped('No approved, active dealer car to exercise the route with.');
        }

        $this->getJson('/api/public/cars/'.$car->slug)->assertOk();
    }

    public function test_the_public_car_detail_never_exposes_dealer_contact_or_balance(): void
    {
        $car = Car::query()
            ->approved()
            ->active()
            ->whereNotNull('slug')
            ->whereNotNull('dealer_id')
            ->first();

        if (! $car) {
            $this->markTestSkipped('No approved, active dealer car to exercise the route with.');
        }

        $payload = $this->getJson('/api/public/cars/'.$car->slug)->json();
        $dealer = data_get($payload, 'data.dealer', data_get($payload, 'dealer'));

        $this->assertIsArray($dealer, 'The response carried no dealer object to check.');

        foreach (['phone', 'email', 'wallet_balance', 'pan_number', 'kyc_document_number'] as $field) {
            $this->assertArrayNotHasKey(
                $field,
                $dealer,
                "The unauthenticated car detail route leaked dealer.$field"
            );
        }
    }
}
