<?php

namespace Tests\Feature\Security;

use App\Models\CustomerChallanSearch;
use App\Models\CustomerServiceHistory;
use App\Models\CustomerVehicleSearch;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * The paid customer lookups cached their results keyed on the vehicle number
 * ALONE. One customer paid for a report; anyone else who typed the same number
 * within 24 hours was served that same report - free, without signing in, and
 * before the charge was even calculated.
 *
 * For the RC lookup that meant handing out the registered owner's name, both
 * addresses and mobile number to a stranger. The challan and service-history
 * lookups leaked the same way. The dealer-side cache was always scoped by
 * dealer_id; only the customer side was not.
 */
class LookupCacheScopeTest extends TestCase
{
    use DatabaseTransactions;

    private const MINE = '9400000001';
    private const SOMEONE_ELSE = '9400000002';

    public static function lookups(): array
    {
        return [
            'RC check' => [CustomerVehicleSearch::class, 'registration_number', 'BR01CACHE1'],
            'challan search' => [CustomerChallanSearch::class, 'vehicle_number', 'BR01CACHE2'],
            'service history' => [CustomerServiceHistory::class, 'vehicle_number', 'BR01CACHE3'],
        ];
    }

    #[DataProvider('lookups')]
    public function test_a_cached_result_is_not_served_to_a_different_customer(string $model, string $column, string $vehicle): void
    {
        $model::create([
            $column => $vehicle,
            'customer_name' => 'Paying Customer',
            'customer_phone' => self::MINE,
            'is_success' => true,
        ]);

        $this->assertNull(
            $model::checkCache($vehicle, self::SOMEONE_ELSE),
            "$model served one customer's paid report to another."
        );
    }

    #[DataProvider('lookups')]
    public function test_a_customer_still_gets_their_own_cached_result(string $model, string $column, string $vehicle): void
    {
        $mine = $model::create([
            $column => $vehicle,
            'customer_name' => 'Paying Customer',
            'customer_phone' => self::MINE,
            'is_success' => true,
        ]);

        $hit = $model::checkCache($vehicle, self::MINE);

        $this->assertNotNull($hit, "$model stopped caching for the customer who paid.");
        $this->assertSame($mine->id, $hit->id);
    }

    #[DataProvider('lookups')]
    public function test_an_unidentified_caller_gets_no_cache_hit(string $model, string $column, string $vehicle): void
    {
        // An empty phone must never match a stored row, or the guard would be
        // trivially bypassed by omitting the field.
        $model::create([
            $column => $vehicle,
            'customer_name' => 'Paying Customer',
            'customer_phone' => self::MINE,
            'is_success' => true,
        ]);

        $this->assertNull($model::checkCache($vehicle, ''));
    }

    public function test_every_customer_lookup_cache_requires_a_customer(): void
    {
        // All five models share the pattern, including the two whose callers
        // were removed - they must not become a trap for the next person.
        $models = [
            CustomerVehicleSearch::class,
            CustomerChallanSearch::class,
            CustomerServiceHistory::class,
            \App\Models\CustomerMarutiServiceHistory::class,
            \App\Models\CustomerMahindraServiceHistory::class,
        ];

        foreach ($models as $model) {
            $method = new \ReflectionMethod($model, 'checkCache');

            $this->assertSame(
                2,
                $method->getNumberOfRequiredParameters(),
                "$model::checkCache() can be called without identifying the customer."
            );
        }
    }
}
