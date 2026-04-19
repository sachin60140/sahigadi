<?php

namespace App\Services;

use App\Models\CustomerChallanSearch;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CustomerChallanSearchService
{
    protected string $apiUrl;

    protected string $secretKey;

    protected string $clientId;

    protected float $chargePerSearch;

    public function __construct()
    {
        $this->apiUrl = 'https://api.invincibleocean.com/invincible/vehicle-challan-detailed';
        $this->secretKey = config('services.service_history_api.secret_key', 'ONsoB7pAonpp1FYJ0Bf6sHFuGLYGEbytHxURPsEmK64gt3HR8yDtxwDafwMuCaonL');
        $this->clientId = config('services.service_history_api.client_id', '8f16f6344cbdcc74620cfdf7c87554f2:045cb470d3ae8da988c7e0982917e1ea');
        $this->chargePerSearch = Setting::getChallanCharge();
    }

    public function getCharge(): float
    {
        return $this->chargePerSearch;
    }

    public function search(string $vehicleNumber, array $customerInfo): array
    {
        $vehicleNum = strtoupper(preg_replace('/[^A-Z0-9]/', '', $vehicleNumber));

        $cached = CustomerChallanSearch::checkCache($vehicleNum);
        if ($cached) {
            return [
                'success' => true,
                'cached' => true,
                'data' => $cached,
                'message' => 'Retrieved from cache (last 24 hours)',
            ];
        }

        try {
            $response = $this->callApi($vehicleNum);
            $result = $this->saveSearch($vehicleNum, $response, $customerInfo);

            return [
                'success' => $result->is_success,
                'cached' => false,
                'data' => $result,
                'message' => $result->is_success ? 'Challan details retrieved successfully' : ($result->error_message ?? 'Unknown error'),
            ];
        } catch (\Exception $e) {
            Log::error('Customer Challan Search Error: '.$e->getMessage());

            $result = $this->saveSearch($vehicleNum, [
                'code' => 500,
                'message' => $e->getMessage(),
            ], $customerInfo);

            return [
                'success' => false,
                'cached' => false,
                'data' => $result,
                'message' => 'Error: '.$e->getMessage(),
            ];
        }
    }

    protected function callApi(string $vehicleNumber): array
    {
        $response = Http::timeout(60)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'secretKey' => $this->secretKey,
                'clientId' => $this->clientId,
            ])
            ->post($this->apiUrl, [
                'vehicleNumber' => $vehicleNumber,
            ]);

        if ($response->successful()) {
            return $response->json();
        }

        $statusCode = $response->status();
        $body = $response->body();

        Log::error('Challan API Error', [
            'status' => $statusCode,
            'body' => $body,
            'vehicleNumber' => $vehicleNumber,
        ]);

        throw new \Exception('API Error ('.$statusCode.'): '.substr($body, 0, 200));
    }

    protected function saveSearch(string $vehicleNum, array $apiResponse, array $customerInfo): CustomerChallanSearch
    {
        $isSuccess = ($apiResponse['code'] ?? 0) == 200;
        $challans = $isSuccess ? ($apiResponse['result'] ?? []) : [];

        $totalAmount = 0;
        $challanCount = count($challans);

        if ($challanCount > 0) {
            foreach ($challans as $challan) {
                $totalAmount += floatval($challan['amountChallan'] ?? 0);
            }
        }

        return CustomerChallanSearch::create([
            'customer_name' => $customerInfo['name'] ?? null,
            'customer_email' => $customerInfo['email'] ?? null,
            'customer_phone' => $customerInfo['phone'] ?? null,
            'vehicle_number' => $vehicleNum,
            'is_success' => $isSuccess,
            'paid_amount' => $this->chargePerSearch,
            'challan_data' => $isSuccess ? $challans : null,
            'total_amount' => $totalAmount,
            'challan_count' => $challanCount,
            'error_message' => $isSuccess ? null : ($apiResponse['message'] ?? 'Unknown error'),
        ]);
    }

    public function updatePaymentInfo(CustomerChallanSearch $search, string $orderId, string $paymentId, float $amount): void
    {
        $search->update([
            'razorpay_order_id' => $orderId,
            'razorpay_payment_id' => $paymentId,
            'paid_amount' => $amount,
        ]);
    }
}
