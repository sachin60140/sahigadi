<?php

namespace Tests\Feature;

use Tests\TestCase;

class ServerRenderedSeoTest extends TestCase
{
    public function test_indexable_pages_ship_complete_metadata_without_javascript(): void
    {
        foreach ([
            '/',
            '/cars',
            '/verified-dealers',
            '/sell-your-car',
            '/contact',
            '/privacy-policy',
            '/terms-of-use',
            '/refund-policy',
        ] as $path) {
            $this->get($path)
                ->assertOk()
                ->assertSee('<title inertia>', false)
                ->assertSee('data-inertia="description"', false)
                ->assertSee('content="index, follow', false)
                ->assertSee('data-inertia="canonical"', false)
                ->assertSee('data-inertia="og-title"', false)
                ->assertSee('data-inertia="og-description"', false);
        }
    }

    public function test_authentication_pages_are_noindex_in_the_initial_html(): void
    {
        foreach ([
            '/admin/login',
            '/dealer/login',
            '/customer/login',
        ] as $path) {
            $this->get($path)
                ->assertOk()
                ->assertSee('content="noindex, nofollow"', false)
                ->assertDontSee('data-inertia="canonical"', false);
        }
    }
}
