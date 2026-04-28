<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\WalletTransaction;
use App\Models\Payment;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class PhonePeService
{
    protected string $clientId;
    protected string $clientSecret;
    protected string $clientVersion;
    protected string $env;

    public function __construct()
    {
        // For V2, the Merchant ID field in our settings is used as Client ID
        $this->clientId = trim(Setting::getPhonePeMerchantId() ?? '');
        // Salt Key is used as Client Secret
        $this->clientSecret = trim(Setting::getPhonePeSaltKey() ?? '');
        // Salt Index is used as Client Version
        $this->clientVersion = trim(Setting::getPhonePeSaltIndex() ?? '1');
        $this->env = trim(Setting::getPhonePeEnvironment() ?? 'UAT');
    }

    protected function getTokenUrl(): string
    {
        $customUrl = Setting::getPhonePeCheckoutUrl();
        if ($customUrl) {
            if (str_contains($customUrl, 'api-preprod.phonepe.com')) {
                return str_replace('/checkout/v2/pay', '/v1/oauth/token', $customUrl);
            } else {
                $tokenUrl = str_replace('/pg/checkout/v2/pay', '/identity-manager/v1/oauth/token', $customUrl);
                if ($tokenUrl !== $customUrl) return $tokenUrl;
                
                return str_replace('/checkout/v2/pay', '/v1/oauth/token', $customUrl);
            }
        }

        return $this->env === 'PRODUCTION'
            ? 'https://api.phonepe.com/apis/identity-manager/v1/oauth/token'
            : 'https://api-preprod.phonepe.com/apis/pg-sandbox/v1/oauth/token';
    }

    protected function getPaymentUrl(): string
    {
        $customUrl = Setting::getPhonePeCheckoutUrl();
        if ($customUrl) {
            return $customUrl;
        }

        return $this->env === 'PRODUCTION'
            ? 'https://api.phonepe.com/apis/pg/checkout/v2/pay'
            : 'https://api-preprod.phonepe.com/apis/pg-sandbox/checkout/v2/pay';
    }

    protected function getStatusUrl(string $transactionId): string
    {
        $paymentUrl = $this->getPaymentUrl();
        return str_replace('/pay', "/order/{$transactionId}/status", $paymentUrl);
    }

    protected function getAccessToken(): string
    {
        $cacheKey = 'phonepe_access_token_' . $this->clientId;
        
        return Cache::remember($cacheKey, 3000, function () {
            $tokenUrl = $this->getTokenUrl();

            $response = Http::asForm()->post($tokenUrl, [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'client_version' => $this->clientVersion,
                'grant_type' => 'client_credentials'
            ]);

            if ($response->successful() && $response->json('access_token')) {
                return $response->json('access_token');
            }

            throw new Exception('PhonePe Authentication Failed: ' . $response->body());
        });
    }

    public function createPayment(float $amount, string $transactionId, array $callbackParams = []): array
    {
        $payload = [
            'merchantOrderId' => $transactionId,
            'amount' => (int) ($amount * 100),
            'paymentFlow' => [
                'type' => 'PG_CHECKOUT',
                'merchantUrls' => [
                    'redirectUrl' => route('dealer.payments.phonepe.callback')
                ]
            ]
        ];

        $paymentUrl = $this->getPaymentUrl();

        $token = $this->getAccessToken();

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'O-Bearer ' . $token
        ])->post($paymentUrl, $payload);

        if ($response->successful()) {
            return [
                'success' => true,
                'redirect_url' => $response->json('redirectUrl')
            ];
        }

        throw new Exception('PhonePe Payment Initiation Failed: ' . $response->body());
    }

    public function verifyPaymentStatus(string $transactionId): array
    {
        $statusUrl = $this->getStatusUrl($transactionId);

        $token = $this->getAccessToken();

        $response = Http::withHeaders([
            'Authorization' => 'O-Bearer ' . $token
        ])->get($statusUrl);

        if ($response->successful()) {
            $data = $response->json();
            return [
                'success' => isset($data['state']) && $data['state'] === 'COMPLETED',
                'status' => $data['state'] ?? 'UNKNOWN',
                'amount' => ($data['amount'] ?? 0) / 100,
                'transaction_id' => $transactionId
            ];
        }

        return [
            'success' => false,
            'status' => 'FAILED',
            'amount' => 0,
            'transaction_id' => $transactionId
        ];
    }

    public function isValidWebhookSignature(string $payload, ?string $signature): bool
    {
        // V2 uses Basic Auth for Webhooks
        return false;
    }

    public function processPayment($dealer, string $transactionId, float $expectedAmount, string $type)
    {
        $status = $this->verifyPaymentStatus($transactionId);

        if (!$status['success']) {
            throw new Exception("PhonePe payment was not successful. Status: " . $status['status']);
        }

        if ($status['amount'] != $expectedAmount) {
            throw new Exception("Payment amount mismatch. Expected: {$expectedAmount}, Received: {$status['amount']}");
        }

        DB::beginTransaction();
        try {
            // Prevent duplicate processing
            $existingTxn = WalletTransaction::where('reference_id', $transactionId)
                ->where('type', 'credit')
                ->lockForUpdate()
                ->first();

            if ($existingTxn) {
                DB::commit();
                return $existingTxn;
            }

            $payment = Payment::updateOrCreate(
                ['phonepe_transaction_id' => $transactionId],
                [
                    'dealer_id' => $dealer->id,
                    'amount' => $status['amount'],
                    'status' => 'completed',
                    'payment_gateway' => 'phonepe',
                    'type' => $type,
                ]
            );

            if ($type === 'wallet_recharge') {
                // Credit the wallet (subtract 18% GST to match razorpay logic)
                $walletCreditAmount = round($status['amount'] / 1.18, 2);
                $walletService = app(\App\Services\WalletService::class);
                $walletService->credit($dealer->id, $walletCreditAmount, 'Wallet recharge via PhonePe', $transactionId, 'payment');
            }

            DB::commit();
            return $payment;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
