<?php

namespace Tests\Unit;

use App\Services\PhonePeService;
use Exception;
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
}
