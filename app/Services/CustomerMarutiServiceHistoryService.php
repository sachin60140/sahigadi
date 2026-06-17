<?php

namespace App\Services;

use App\Models\CustomerMarutiServiceHistory;
use App\Models\CustomerMarutiServiceHistoryRecord;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CustomerMarutiServiceHistoryService
{
    protected string $apiUrl;

    protected string $secretKey;

    protected string $clientId;

    protected float $chargePerSearch;

    public function __construct()
    {
        $this->apiUrl = 'https://api.invincibleocean.com/invincible/vehicleMarutiServiceHistory';
        $this->secretKey = config('services.service_history_api.secret_key', 'ONsoB7pAonpp1FYJ0Bf6sHFuGLYGEbytHxURPsEmK64gt3HR8yDtxwDafwMuCaonL');
        $this->clientId = config('services.service_history_api.client_id', '8f16f6344cbdcc74620cfdf7c87554f2:045cb470d3ae8da988c7e0982917e1ea');
        $this->chargePerSearch = Setting::getMarutiServiceHistoryCharge();
    }

    public function getCharge(): float
    {
        return $this->chargePerSearch;
    }

    public function search(string $vehicleNumber, array $customerInfo): array
    {
        $vehicleNum = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $vehicleNumber));

        $vehicleNum = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $vehicleNumber));

        // Cache disabled per user request: "search always fresh no old record required"

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
            Log::error('Customer Maruti Service History Search Error: '.$e->getMessage());

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

        Log::error('Maruti Service History API Error', [
            'status' => $statusCode,
            'body' => $body,
            'vehicleNumber' => $vehicleNumber,
        ]);

        throw new \Exception('API Error ('.$statusCode.'): '.substr($body, 0, 200));
    }

    protected function saveServiceHistory(string $vehicleNum, array $apiResponse, array $customerInfo): CustomerMarutiServiceHistory
    {
        $isSuccess = ($apiResponse['code'] ?? 0) == 200;
        $result = $apiResponse['result'] ?? null;
        $serviceRecords = $result['serviceHistoryDetails'] ?? [];

        $serviceHistory = CustomerMarutiServiceHistory::create([
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
            CustomerMarutiServiceHistoryRecord::create([
                'cust_maruti_service_id' => $serviceHistory->id,
                'mileage' => $record['mileage'] ?? null,
                'total_amount' => $record['totalAmount'] ?? null,
                'dealer_code' => $record['dealerNo'] ?? null,
                'dealer_name' => $record['dealerName'] ?? null,
                'dealer_address' => $record['dealerAddress'] ?? null,
                'repair_order_bill_date' => $this->parseFlexibleDate($record['dateOfBill'] ?? null),
                'repair_order_bill_no' => $record['noOfRO'] ?? null,
                'svc_date' => $this->parseFlexibleDate($record['dateOfSVC'] ?? null),
                'repair_order_no' => $record['noOfRO'] ?? null,
                'register_no' => $record['noOfJobCard'] ?? null,
                'service_assistant_name' => $record['nameOfSA'] ?? null,
                'work_type' => $record['serviceType'] ?? null,
                'status' => $record['typOfPayment'] ?? null,
                'service_cate' => $record['serviceType'] ?? null,
                'chassis_no' => $record['srVehicleCd'] ?? null,
                'location_code' => $record['cdLoc'] ?? null,
                'part_amount' => $record['partAmmount'] ?? null,
                'labour_amount' => $record['labourAmmount'] ?? null,
            ]);
        }

        return $serviceHistory;
    }

    protected function parseFlexibleDate(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }
        try {
            return \Carbon\Carbon::parse(str_replace('/', '-', $value))->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    public function updatePaymentInfo(CustomerMarutiServiceHistory $serviceHistory, string $orderId, string $paymentId, float $amount): void
    {
        $serviceHistory->update([
            'razorpay_order_id' => $orderId,
            'razorpay_payment_id' => $paymentId,
            'paid_amount' => $amount,
        ]);
    }
}
