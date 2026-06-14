<?php

namespace Tests\Feature;

use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class InertiaPageContractTest extends TestCase
{
    public function test_public_legal_pages_use_the_shared_vue_page(): void
    {
        foreach ([
            'privacy-policy' => 'Privacy Policy',
            'terms-of-use' => 'Terms of Use',
            'refund-policy' => 'Refund Policy',
        ] as $routeName => $title) {
            $this->get(route($routeName))
                ->assertOk()
                ->assertInertia(fn (Assert $page) => $page
                    ->component('Public/LegalPage')
                    ->where('title', $title)
                    ->has('sections')
                );
        }
    }

    public function test_authentication_entry_pages_are_vue_pages(): void
    {
        foreach ([
            'admin.login' => 'Admin/Auth/Login',
            'dealer.login' => 'Auth/DealerLogin',
            'customer.login' => 'Auth/CustomerLogin',
        ] as $routeName => $component) {
            $this->get(route($routeName))
                ->assertOk()
                ->assertInertia(fn (Assert $page) => $page->component($component));
        }
    }

    public function test_customer_login_uses_registered_otp_routes(): void
    {
        $this->get(route('customer.login'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Auth/CustomerLogin')
                ->where('actions.sendOtp', route('customer.send-otp'))
                ->where('actions.verifyOtp', route('customer.verify-otp'))
            );

        $this->postJson(route('customer.send-otp'), [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('phone');

        $this->postJson(route('customer.verify-otp'), [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['phone', 'otp']);
    }
}
