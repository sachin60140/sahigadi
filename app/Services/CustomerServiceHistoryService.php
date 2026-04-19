<?php

namespace App\Services;

use App\Models\CustomerServiceHistory;
use App\Models\CustomerServiceHistoryRecord;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CustomerServiceHistoryService
{
    protected string $apiUrl;

    protected string $secretKey;

    protected string $clientId;

    protected float $chargePerSearch;

    public function __construct()
    {
        $this->apiUrl = config('services.service_history_api.url', 'https://api.invincibleocean.com/invincible/mahindra-service-history');
        $this->secretKey = config('services.service_history_api.secret_key', 'ONsoB7pAonpp1FYJ0Bf6sHFuGLYGEbytHxURPsEmK64gt3HR8yDtxwDafwMuCaonL');
        $this->clientId = config('services.service_history_api.client_id', '8f16f6344cbdcc74620cfdf7c87554f2:045cb470d3ae8da988c7e0982917e1ea');
        $this->chargePerSearch = Setting::getServiceHistoryCharge();
    }

    public function getCharge(): float
    {
        return $this->chargePerSearch;
    }

    public function search(string $vehicleNumber, array $customerInfo): array
    {
        $vehicleNum = strtoupper(preg_replace('/[^A-Z0-9]/', '', $vehicleNumber));

        $cached = CustomerServiceHistory::checkCache($vehicleNum);
        if ($cached) {
            $cached->load('records');

            return [
                'success' => true,
                'cached' => true,
                'data' => $cached,
                'message' => 'Retrieved from cache (last 24 hours)',
            ];
        }

        try {
            $response = $this->callApi($vehicleNum);
            $serviceHistory = $this->saveServiceHistory($vehicleNum, $response, $customerInfo);

            return [
                'success' => $serviceHistory->is_success,
                'cached' => false,
                'data' => $serviceHistory,
                'message' => $serviceHistory->is_success ? 'Service history retrieved successfully' : ($serviceHistory->error_message ?? 'Unknown error'),
            ];
        } catch (\Exception $e) {
            Log::error('Customer Service History Search Error: '.$e->getMessage());

            $serviceHistory = $this->saveServiceHistory($vehicleNum, [
                'code' => 500,
                'message' => $e->getMessage(),
            ], $customerInfo);

            return [
                'success' => false,
                'cached' => false,
                'data' => $serviceHistory,
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

        Log::error('Service History API Error', [
            'status' => $statusCode,
            'body' => $body,
            'vehicleNumber' => $vehicleNumber,
        ]);

        throw new \Exception('API Error ('.$statusCode.'): '.substr($body, 0, 200));
    }

    protected function saveServiceHistory(string $vehicleNum, array $apiResponse, array $customerInfo): CustomerServiceHistory
    {
        $isSuccess = ($apiResponse['code'] ?? 0) == 200;
        $result = $apiResponse['result'] ?? null;
        $serviceRecords = $result['serviceHistoryDetails'] ?? [];

        $serviceHistory = CustomerServiceHistory::create([
            'customer_name' => $customerInfo['name'] ?? null,
            'customer_email' => $customerInfo['email'] ?? null,
            'customer_phone' => $customerInfo['phone'] ?? null,
            'vehicle_number' => $vehicleNum,
            'is_success' => $isSuccess,
            'paid_amount' => $this->chargePerSearch,
            'error_message' => $isSuccess ? null : ($apiResponse['message'] ?? 'Unknown error'),
            'raw_response' => $apiResponse,
        ]);

        foreach ($serviceRecords as $record) {
            CustomerServiceHistoryRecord::create([
                'customer_service_history_id' => $serviceHistory->id,
                'chassis_no' => $record['chassis_no'] ?? null,
                'location_code' => $record['location_code'] ?? null,
                'location_name' => $record['location_name'] ?? null,
                'mileage' => $record['mileage'] ?? null,
                'net_bill_amt' => $record['net_bill_amt'] ?? null,
                'online_payment_flag' => $record['online_payment_flag'] ?? null,
                'out_standing_amt' => $record['out_standing_amt'] ?? null,
                'paid_amt' => $record['paid_amt'] ?? null,
                'dealer_code' => $record['dealer_code'] ?? null,
                'dealer_name' => $record['dealer_name'] ?? null,
                'repair_order_bill_date' => $record['repair_order_bill_date'] ?? null,
                'repair_order_bill_no' => $record['repair_order_bill_no'] ?? null,
                'svc_date' => $record['svc_date'] ?? null,
                'repair_order_no' => $record['repair_order_no'] ?? null,
                'register_no' => $record['register_no'] ?? null,
                'service_assistant_no' => $record['service_assistant_no'] ?? null,
                'service_assistant_name' => $record['service_assistant_name'] ?? null,
                'work_type' => $record['work_type'] ?? null,
                'status' => $record['status'] ?? null,
                'service_cate' => $record['service_cate'] ?? null,
            ]);
        }

        return $serviceHistory;
    }

    public function updatePaymentInfo(CustomerServiceHistory $serviceHistory, string $orderId, string $paymentId, float $amount): void
    {
        $serviceHistory->update([
            'razorpay_order_id' => $orderId,
            'razorpay_payment_id' => $paymentId,
            'paid_amount' => $amount,
        ]);
    }
}
