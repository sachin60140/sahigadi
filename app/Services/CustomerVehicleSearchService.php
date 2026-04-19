<?php

namespace App\Services;

use App\Models\CustomerVehicleSearch;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CustomerVehicleSearchService
{
    protected string $apiUrl;

    protected string $apiKey;

    protected string $provider;

    protected float $chargePerSearch;

    public function __construct()
    {
        $this->apiUrl = config('services.vehicle_api.url', 'https://api.attestr.com/api/v2/public/checkx/rc');
        $this->apiKey = config('services.vehicle_api.key', '');
        $this->provider = config('services.vehicle_api.provider', 'attestr');
        $this->chargePerSearch = Setting::getVehicleSearchCharge();
    }

    public function getCharge(): float
    {
        return $this->chargePerSearch;
    }

    public function search(string $registrationNumber, array $customerInfo): array
    {
        $regNumber = strtoupper(preg_replace('/[^A-Z0-9]/', '', $registrationNumber));

        $cached = CustomerVehicleSearch::checkCache($regNumber);
        if ($cached) {
            return [
                'success' => true,
                'cached' => true,
                'data' => $cached,
                'message' => 'Retrieved from cache (last 24 hours)',
            ];
        }

        try {
            $response = $this->callApi($regNumber);
            $result = $this->saveSearch($regNumber, $response, $customerInfo);

            return [
                'success' => $result->is_success,
                'cached' => false,
                'data' => $result,
                'message' => $result->is_success ? 'Vehicle details retrieved successfully' : ($result->error_message ?? 'Unknown error'),
            ];
        } catch (\Exception $e) {
            Log::error('Customer Vehicle Search Error: '.$e->getMessage());

            $result = $this->saveSearch($regNumber, [
                'success' => false,
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

    protected function callApi(string $registrationNumber): array
    {
        $response = Http::timeout(60)
            ->withHeaders([
                'Authorization' => 'Bearer '.$this->apiKey,
                'Content-Type' => 'application/json',
            ])
            ->post($this->apiUrl, [
                'reg_no' => $registrationNumber,
            ]);

        if ($response->successful()) {
            $data = $response->json();

            return [
                'success' => $data['status'] ?? false,
                'data' => $data,
            ];
        }

        return [
            'success' => false,
            'message' => 'API Error: '.$response->status(),
        ];
    }

    protected function saveSearch(string $regNumber, array $apiResponse, array $customerInfo): CustomerVehicleSearch
    {
        $isSuccess = $apiResponse['success'] ?? false;
        $vehicleData = $isSuccess ? ($apiResponse['data'] ?? null) : null;

        return CustomerVehicleSearch::create([
            'customer_name' => $customerInfo['name'] ?? null,
            'customer_email' => $customerInfo['email'] ?? null,
            'customer_phone' => $customerInfo['phone'] ?? null,
            'registration_number' => $regNumber,
            'is_success' => $isSuccess,
            'paid_amount' => $this->chargePerSearch,
            'vehicle_data' => $vehicleData,
            'error_message' => $isSuccess ? null : ($apiResponse['message'] ?? 'Unknown error'),
        ]);
    }

    public function updatePaymentInfo(CustomerVehicleSearch $search, string $orderId, string $paymentId, float $amount): void
    {
        $search->update([
            'razorpay_order_id' => $orderId,
            'razorpay_payment_id' => $paymentId,
            'paid_amount' => $amount,
        ]);
    }
}
