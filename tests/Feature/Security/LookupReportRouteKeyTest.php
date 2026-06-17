<?php

namespace Tests\Feature\Security;

use App\Models\CustomerMahindraServiceHistory;
use App\Models\CustomerMarutiServiceHistory;
use App\Models\CustomerServiceHistory;
use App\Models\CustomerVehicleSearch;
use Tests\TestCase;

class LookupReportRouteKeyTest extends TestCase
{
    /**
     * Paid lookup reports contain customer PII and are reachable on public
     * routes. They must bind by an unguessable uuid, never the sequential id,
     * so the records cannot be enumerated (IDOR regression guard for H1).
     */
    public function test_public_lookup_models_are_bound_by_uuid_not_sequential_id(): void
    {
        $models = [
            CustomerVehicleSearch::class,
            CustomerServiceHistory::class,
            CustomerMarutiServiceHistory::class,
            CustomerMahindraServiceHistory::class,
        ];

        foreach ($models as $model) {
            $this->assertSame(
                'uuid',
                (new $model)->getRouteKeyName(),
                $model.' must be route-bound by uuid to prevent enumeration of paid PII reports.'
            );
        }
    }
}
