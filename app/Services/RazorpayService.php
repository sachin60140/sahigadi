<?php

namespace App\Services;

use App\Models\Dealer;
use App\Models\Payment;
use App\Models\Setting;
use Exception;
use Illuminate\Support\Facades\DB;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

class RazorpayService
{
    protected $api;

    public function __construct()
    {
        $keyId = Setting::getRazorpayKeyId() ?? config('services.razorpay.key');
        $keySecret = Setting::getRazorpayKeySecret() ?? config('services.razorpay.secret');
        $this->api = new Api($keyId, $keySecret);
    }

    public function createOrder(float $amount, string $receipt, array $notes = []): array
    {
        $order = $this->api->order->create([
            'amount' => (int) ($amount * 100),
            'currency' => 'INR',
            'receipt' => $receipt,
            'notes' => $notes,
        ]);

        return [
            'order_id' => $order->id,
            'amount' => $order->amount / 100,
            'currency' => $order->currency,
        ];
    }

    public function verifySignature(string $razorpayOrderId, string $razorpayPaymentId, string $razorpaySignature): bool
    {
        try {
            $attributes = [
                'razorpay_order_id' => $razorpayOrderId,
                'razorpay_payment_id' => $razorpayPaymentId,
                'razorpay_signature' => $razorpaySignature,
            ];

            $this->api->utility->verifyPaymentSignature($attributes);

            return true;
        } catch (SignatureVerificationError $e) {
            return false;
        }
    }

    public function processPayment(
        Dealer $dealer,
        string $razorpayOrderId,
        string $razorpayPaymentId,
        string $razorpaySignature,
        float $amount,
        string $type,
        ?string $referenceId = null
    ): Payment {
        if (! $this->verifySignature($razorpayOrderId, $razorpayPaymentId, $razorpaySignature)) {
            throw new Exception('Invalid payment signature');
        }

        $existingPayment = Payment::where('razorpay_payment_id', $razorpayPaymentId)->first();
        if ($existingPayment) {
            if ($existingPayment->status === 'completed') {
                $existingPayment->update(['is_duplicate' => true]);
                throw new Exception('Payment already processed');
            }
        }

        return DB::transaction(function () use ($dealer, $razorpayOrderId, $razorpayPaymentId, $razorpaySignature, $amount, $type, $referenceId) {
            if ($type === 'wallet_recharge') {
                $walletService = app(WalletService::class);
                
                // Subtract the 18% GST back out to compute the actual recharge amount
                $walletCreditAmount = round($amount / 1.18, 2);

                $walletService->credit(
                    $dealer->id,
                    $walletCreditAmount,
                    'Wallet recharge via Razorpay',
                    $razorpayPaymentId,
                    'payment'
                );
            }

            $payment = Payment::updateOrCreate(
                ['razorpay_order_id' => $razorpayOrderId],
                [
                    'dealer_id' => $dealer->id,
                    'razorpay_payment_id' => $razorpayPaymentId,
                    'razorpay_signature' => $razorpaySignature,
                    'amount' => $amount,
                    'status' => 'completed',
                    'type' => $type,
                    'reference_id' => $referenceId,
                ]
            );

            return $payment;
        });
    }

    public function getKeyId(): string
    {
        return Setting::getRazorpayKeyId() ?? '';
    }

    public function refundPayment(string $paymentId, ?float $amount = null): array
    {
        try {
            $payment = $this->api->payment->fetch($paymentId);
            $options = [];
            if ($amount !== null) {
                $options['amount'] = (int) ($amount * 100);
            }
            $refund = $payment->refund($options);
            
            return [
                'success' => true,
                'refund_id' => $refund->id,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
