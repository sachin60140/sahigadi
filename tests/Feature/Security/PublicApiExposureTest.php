<?php

namespace Tests\Feature\Security;

use App\Models\Car;
use App\Models\CustomerCarListing;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * The three /api/public/* routes returned Eloquent models straight to an
 * unauthenticated caller, so every field on the row was serialised. That put
 * the whole catalogue's seller phone numbers, WhatsApp numbers, email
 * addresses and full registration plates behind a single curl, with no auth
 * and no rate limit -- while the website shows only a masked mobile and puts
 * the real one behind the OTP contact unlock, and while the dealer API bills
 * per RC lookup on exactly those plate numbers.
 *
 * The internal moderation fields (status reasons, soft-delete timestamps) went
 * out with them.
 */
class PublicApiExposureTest extends TestCase
{
    use DatabaseTransactions;

    /** Fields that must never reach an unauthenticated caller. */
    private const NEVER_PUBLIC = [
        'owner_phone',
        'owner_email',
        'whatsapp_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'registration_number',
        'rejection_reason',
        'deleted_at',
    ];

    public function test_the_home_payload_carries_no_seller_contact_details(): void
    {
        $payload = $this->getJson('/api/public/home')->assertOk()->json('data');

        foreach (['featured_cars', 'recent_listings'] as $group) {
            foreach ($payload[$group] ?? [] as $row) {
                $this->assertNoPrivateFields($row, "public/home > $group");
            }
        }
    }

    public function test_the_car_list_carries_no_seller_contact_details(): void
    {
        $rows = $this->getJson('/api/public/cars')->assertOk()->json('data.data');

        foreach ($rows ?? [] as $row) {
            $this->assertNoPrivateFields($row, 'public/cars');
        }
    }

    public function test_a_customer_listing_detail_carries_no_contact_details(): void
    {
        $slug = CustomerCarListing::query()->approved()->active()->value('slug');

        if (! $slug) {
            $this->markTestSkipped('No approved, active customer listing to exercise.');
        }

        $row = $this->getJson('/api/public/cars/'.$slug)->assertOk()->json('data');

        $this->assertNoPrivateFields($row, 'public/cars/{slug} customer');

        // The seller phone also reached the client a second way, through an
        // eager-loaded customer relation that nothing in the app reads.
        $this->assertArrayNotHasKey('customer', $row, 'carDetail re-exposed the seller via the customer relation.');
    }

    public function test_a_dealer_car_detail_carries_no_contact_details(): void
    {
        $slug = Car::query()->approved()->active()->whereNotNull('dealer_id')->value('slug');

        if (! $slug) {
            $this->markTestSkipped('No approved, active dealer car to exercise.');
        }

        $row = $this->getJson('/api/public/cars/'.$slug)->assertOk()->json('data');

        $this->assertNoPrivateFields($row, 'public/cars/{slug} dealer');
        $this->assertArrayNotHasKey('wallet_balance', $row['dealer'] ?? []);
        $this->assertArrayNotHasKey('phone', $row['dealer'] ?? []);
    }

    #[DataProvider('hostilePagination')]
    public function test_hostile_pagination_never_returns_a_server_error(string $query, int $expectedPerPage): void
    {
        $response = $this->getJson('/api/public/cars?'.$query)->assertOk();

        $this->assertSame(
            $expectedPerPage,
            (int) $response->json('data.per_page'),
            "?$query produced the wrong page size"
        );
    }

    public static function hostilePagination(): array
    {
        return [
            // A non-numeric per_page reached Collection::forPage() as a string
            // and 500'd the endpoint for anyone who sent it.
            'non-numeric per_page' => ['per_page=abc', 15],
            'non-numeric page' => ['page=xyz', 15],
            'array injection' => ['per_page[]=1', 15],
            // An uncapped per_page turned one request into a full-table dump.
            'oversized per_page' => ['per_page=100000', 50],
            'negative per_page' => ['per_page=-5', 1],
            'honours a sane value' => ['per_page=25', 25],
        ];
    }

    public function test_the_public_catalogue_routes_are_rate_limited(): void
    {
        foreach (['api/public/home', 'api/public/cars', 'api/public/cars/{slug}'] as $uri) {
            $route = collect(Route::getRoutes())->first(fn ($r) => $r->uri() === $uri && in_array('GET', $r->methods()));

            $this->assertNotNull($route, "$uri is not registered");

            $throttles = array_filter(
                $route->gatherMiddleware(),
                fn ($m) => is_string($m) && str_starts_with($m, 'throttle:')
            );

            $this->assertNotEmpty($throttles, "$uri carries no throttle middleware");
        }
    }

    private function assertNoPrivateFields(mixed $row, string $where): void
    {
        if (! is_array($row)) {
            return;
        }

        foreach (self::NEVER_PUBLIC as $field) {
            $this->assertArrayNotHasKey($field, $row, "$where leaked $field to an unauthenticated caller");
        }
    }
}
