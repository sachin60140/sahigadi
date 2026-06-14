<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Dealer;
use App\Models\Setting;
use App\Services\PhonePeService;
use App\Services\RazorpayService;
use App\Services\SubscriptionService;
use App\Services\WalletService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Mockery;
use Tests\TestCase;

class PhonePeInitiationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_dealer_checkout_returns_a_phonepe_paypage_url_to_vue(): void
    {
        Setting::setIsPhonePeActive(true);

        $dealer = new Dealer([
            'name' => 'Test Dealer',
            'email' => 'dealer@example.test',
            'status' => 'approved',
        ]);
        $dealer->id = 42;

        $phonePe = Mockery::mock(PhonePeService::class);
        $phonePe->shouldReceive('createPayment')
            ->once()
            ->withArgs(function ($amount, $transactionId, $params) {
                return $amount === 118.0
                    && preg_match('/^PP_42_[A-Z0-9]{26}$/', $transactionId) === 1
                    && str_contains($params['redirectUrl'], 'merchant_order_id='.$transactionId);
            })
            ->andReturn([
                'success' => true,
                'redirect_url' => 'https://mercury-uat.phonepe.com/transact/test',
            ]);

        $this->app->instance(PhonePeService::class, $phonePe);
        $this->app->instance(RazorpayService::class, Mockery::mock(RazorpayService::class));
        $this->app->instance(WalletService::class, Mockery::mock(WalletService::class));
        $this->app->instance(SubscriptionService::class, Mockery::mock(SubscriptionService::class));

        $intent = '43a09e62-77bb-4c36-87a6-555ed177fba1';

        $this->actingAs($dealer, 'dealer')
            ->withSession([
                "dealer_payment_intents.{$intent}" => [
                    'dealer_id' => 42,
                    'type' => 'wallet_recharge',
                    'amount' => 118.0,
                    'plan_id' => null,
                    'car_id' => null,
                    'days' => null,
                ],
            ])
            ->postJson(route('dealer.payments.phonepe.initiate'), ['intent' => $intent])
            ->assertOk()
            ->assertJsonPath('checkout_url', 'https://mercury-uat.phonepe.com/transact/test');
    }

    public function test_customer_checkout_returns_a_phonepe_paypage_url_to_vue(): void
    {
        Setting::setIsPhonePeActive(true);

        $customer = new Customer([
            'name' => 'Test Customer',
            'email' => 'customer@example.test',
            'profile_completion_percentage' => 100,
        ]);
        $customer->id = 84;

        $phonePe = Mockery::mock(PhonePeService::class);
        $phonePe->shouldReceive('createPayment')
            ->once()
            ->withArgs(function ($amount, $transactionId, $params) {
                return $amount === 236.0
                    && preg_match('/^PPC_84_[A-Z0-9]{26}$/', $transactionId) === 1
                    && str_contains($params['redirectUrl'], 'merchant_order_id='.$transactionId);
            })
            ->andReturn([
                'success' => true,
                'redirect_url' => 'https://mercury-uat.phonepe.com/transact/customer-test',
            ]);

        $this->app->instance(PhonePeService::class, $phonePe);
        $this->app->instance(RazorpayService::class, Mockery::mock(RazorpayService::class));

        $intent = '2328884e-0c15-4dd9-8d62-6acb3a83b1c6';

        $this->actingAs($customer, 'customer')
            ->withSession([
                "customer_payment_intents.{$intent}" => [
                    'customer_id' => 84,
                    'type' => 'wallet_recharge',
                    'amount' => 236.0,
                ],
            ])
            ->postJson(route('customer.payments.phonepe.initiate'), ['intent' => $intent])
            ->assertOk()
            ->assertJsonPath('checkout_url', 'https://mercury-uat.phonepe.com/transact/customer-test');
    }
}
