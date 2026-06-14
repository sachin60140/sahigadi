<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Dealer;
use App\Models\Payment;
use App\Services\CustomerChallanSearchService;
use App\Services\CustomerServiceHistoryService;
use App\Services\CustomerVehicleSearchService;
use App\Services\PhonePeService;
use App\Services\RazorpayService;
use App\Services\SubscriptionService;
use App\Services\WalletService;
use Mockery;
use Tests\TestCase;

class PaymentCallbackIntegrityTest extends TestCase
{
    public function test_dealer_razorpay_callback_uses_the_server_owned_amount(): void
    {
        $dealer = new Dealer([
            'name' => 'Test Dealer',
            'email' => 'dealer@example.test',
            'status' => 'approved',
        ]);
        $dealer->id = 42;

        $razorpay = Mockery::mock(RazorpayService::class);
        $razorpay->shouldReceive('processPayment')
            ->once()
            ->withArgs(fn ($owner, $orderId, $paymentId, $signature, $amount, $type) =>
                $owner === $dealer
                && $orderId === 'order_server'
                && $paymentId === 'pay_123'
                && $signature === 'signature'
                && $amount === 118.0
                && $type === 'wallet_recharge'
            )
            ->andReturn(new Payment());

        $this->bindPaymentControllerDependencies($razorpay);

        $this->actingAs($dealer, 'dealer')
            ->withSession([
                'dealer_razorpay_orders' => [
                    'order_server' => [
                        'dealer_id' => 42,
                        'type' => 'wallet_recharge',
                        'amount' => 118.0,
                        'plan_id' => null,
                        'car_id' => null,
                        'days' => null,
                    ],
                ],
            ])
            ->post(route('dealer.payments.success'), [
                'razorpay_order_id' => 'order_server',
                'razorpay_payment_id' => 'pay_123',
                'razorpay_signature' => 'signature',
                'amount' => 1,
            ])
            ->assertRedirect(route('dealer.wallet.index'))
            ->assertSessionHas('success');
    }

    public function test_customer_razorpay_callback_rejects_an_order_from_another_session(): void
    {
        $customer = new Customer([
            'name' => 'Test Customer',
            'email' => 'customer@example.test',
            'profile_completion_percentage' => 100,
        ]);
        $customer->id = 84;

        $razorpay = Mockery::mock(RazorpayService::class);
        $razorpay->shouldNotReceive('processPayment');
        $this->app->instance(RazorpayService::class, $razorpay);
        $this->app->instance(PhonePeService::class, Mockery::mock(PhonePeService::class));

        $this->actingAs($customer, 'customer')
            ->withSession([
                'customer_razorpay_orders' => [
                    'order_expected' => [
                        'customer_id' => 84,
                        'type' => 'wallet_recharge',
                        'amount' => 118.0,
                    ],
                ],
            ])
            ->post(route('customer.payments.success'), [
                'razorpay_order_id' => 'order_other',
                'razorpay_payment_id' => 'pay_123',
                'razorpay_signature' => 'signature',
            ])
            ->assertRedirect(route('customer.wallet.index'))
            ->assertSessionHas('error', 'This payment session is invalid or has expired.');
    }

    public function test_service_history_callback_rejects_a_mismatched_order_before_api_work(): void
    {
        $this->assertPublicServiceRejectsMismatchedOrder(
            'service-history.callback',
            'service_history_pending',
            CustomerServiceHistoryService::class
        );
    }

    public function test_vehicle_search_callback_rejects_a_mismatched_order_before_api_work(): void
    {
        $this->assertPublicServiceRejectsMismatchedOrder(
            'vehicle-search.callback',
            'vehicle_search_pending',
            CustomerVehicleSearchService::class
        );
    }

    public function test_challan_callback_rejects_a_mismatched_order_before_api_work(): void
    {
        $this->assertPublicServiceRejectsMismatchedOrder(
            'challan-search.callback',
            'challan_search_pending',
            CustomerChallanSearchService::class
        );
    }

    private function bindPaymentControllerDependencies(RazorpayService $razorpay): void
    {
        $this->app->instance(RazorpayService::class, $razorpay);
        $this->app->instance(PhonePeService::class, Mockery::mock(PhonePeService::class));
        $this->app->instance(WalletService::class, Mockery::mock(WalletService::class));
        $this->app->instance(SubscriptionService::class, Mockery::mock(SubscriptionService::class));
    }

    private function assertPublicServiceRejectsMismatchedOrder(
        string $routeName,
        string $sessionKey,
        string $serviceClass
    ): void {
        $razorpay = Mockery::mock(RazorpayService::class);
        $razorpay->shouldNotReceive('verifySignature');

        $service = Mockery::mock($serviceClass);
        $service->shouldNotReceive('search');

        $this->app->instance(RazorpayService::class, $razorpay);
        $this->app->instance($serviceClass, $service);

        $this->withSession([
            $sessionKey => [
                'order_id' => 'order_expected',
                'vehicle_number' => 'BR01AB1234',
                'registration_number' => 'BR01AB1234',
                'customer_info' => [
                    'name' => 'Test Customer',
                    'phone' => '9999999999',
                    'email' => null,
                ],
            ],
        ])->post(route($routeName), [
            'razorpay_order_id' => 'order_other',
            'razorpay_payment_id' => 'pay_123',
            'razorpay_signature' => 'signature',
        ])->assertRedirect()
            ->assertSessionHas('error', 'Payment order does not match this search');
    }
}
