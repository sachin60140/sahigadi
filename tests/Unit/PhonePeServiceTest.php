<?php

namespace Tests\Unit;

use App\Services\PhonePeService;
use Exception;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Mockery;
use Tests\TestCase;

class PhonePeServiceTest extends TestCase
{
    public function test_process_payment_rejects_a_gateway_amount_mismatch(): void
    {
        $service = Mockery::mock(PhonePeService::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $service->shouldReceive('verifyPaymentStatus')
            ->once()
            ->with('transaction_123')
            ->andReturn([
                'success' => true,
                'status' => 'COMPLETED',
                'amount' => 99.0,
                'transaction_id' => 'transaction_123',
                'provider_reference_id' => 'provider_123',
            ]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Payment amount mismatch. Expected: 118');

        $service->processPayment(null, 'transaction_123', 118.0, 'custom_payment_link');
    }

    public function test_create_payment_returns_the_phonepe_checkout_url(): void
    {
        $paymentUrl = 'https://api-preprod.phonepe.com/apis/pg-sandbox/checkout/v2/pay';
        $checkoutUrl = 'https://mercury-uat.phonepe.com/transact/uat_v2?token=test-token';

        Http::fake([
            $paymentUrl => Http::response([
                'orderId' => 'OMO123',
                'state' => 'PENDING',
                'redirectUrl' => $checkoutUrl,
            ]),
        ]);

        $service = Mockery::mock(PhonePeService::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $service->shouldReceive('getPaymentUrl')->andReturn($paymentUrl);
        $service->shouldReceive('getAccessToken')->andReturn('access-token');
        $service->shouldReceive('assertRedirectUrl')->andReturnNull();

        $result = $service->createPayment(
            118.00,
            'PP_42_01JTESTORDER123456789012',
            ['redirectUrl' => 'https://example.test/dealer/payments/phonepe/callback']
        );

        $this->assertTrue($result['success']);
        $this->assertSame('PENDING', $result['state']);
        $this->assertSame($checkoutUrl, $result['redirect_url']);

        Http::assertSent(fn (Request $request) =>
            $request->url() === $paymentUrl
            && $request->hasHeader('Authorization', 'O-Bearer access-token')
            && $request['merchantOrderId'] === 'PP_42_01JTESTORDER123456789012'
            && $request['amount'] === 11800
            && $request['paymentFlow']['type'] === 'PG_CHECKOUT'
        );
    }

    public function test_order_status_reads_the_v2_payment_reference(): void
    {
        $statusUrl = 'https://api-preprod.phonepe.com/apis/pg-sandbox/checkout/v2/order/PP_42_TEST/status';

        Http::fake([
            $statusUrl.'*' => Http::response([
                'orderId' => 'OMO123',
                'state' => 'COMPLETED',
                'amount' => 11800,
                'paymentDetails' => [
                    [
                        'transactionId' => 'OM123',
                        'state' => 'COMPLETED',
                    ],
                ],
            ]),
        ]);

        $service = Mockery::mock(PhonePeService::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $service->shouldReceive('getStatusUrl')->andReturn($statusUrl);
        $service->shouldReceive('getAccessToken')->andReturn('access-token');

        $result = $service->verifyPaymentStatus('PP_42_TEST');

        $this->assertTrue($result['success']);
        $this->assertSame('COMPLETED', $result['status']);
        $this->assertSame(118.0, $result['amount']);
        $this->assertSame('OM123', $result['provider_reference_id']);
    }

    public function test_create_payment_rejects_an_order_id_longer_than_phonepe_allows(): void
    {
        $service = Mockery::mock(PhonePeService::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('PhonePe merchant order ID is invalid.');

        $service->createPayment(
            10,
            str_repeat('A', 64),
            ['redirectUrl' => 'https://example.test/phonepe/callback']
        );
    }
}
