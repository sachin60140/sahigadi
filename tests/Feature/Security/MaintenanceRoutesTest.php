<?php

namespace Tests\Feature\Security;

use Tests\TestCase;

class MaintenanceRoutesTest extends TestCase
{
    public function test_public_maintenance_endpoints_are_not_registered(): void
    {
        foreach ([
            '/run-live-migration',
            '/run-migrations',
            '/optimize-images',
            '/test-cron',
        ] as $path) {
            $this->get($path)->assertNotFound();
        }
    }
}
