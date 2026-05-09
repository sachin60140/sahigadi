<?php

namespace App\Services;

use App\Models\ChallanPdfSearch;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChallanPdfService
{
    /**
     * Search and generate Challan PDF.
     * 
     * @param string $vehicleNumber
     * @param mixed $user (Customer or Dealer model instance)
     * @param string $userType ('customer' or 'dealer')
     * @return array
     */
    public function fetchChallanPdf(string $vehicleNumber, $user, string $userType): array
    {
        // 1. Check if service is active
        if (!Setting::isChallanPdfActive()) {
            return ['success' => false, 'message' => 'Challan PDF Service is currently disabled.'];
        }

        // 2. Determine charge amount
        $chargeAmount = $userType === 'customer' 
            ? Setting::getChallanPdfCharge() 
            : Setting::getDealerChallanPdfCharge();

        // 3. Check wallet balance
        if ($user->wallet->balance < $chargeAmount) {
            return [
                'success' => false, 
                'message' => 'Low Balance', 
                'redirect_to_recharge' => true
            ];
        }

        // 4. Hit API
        $apiUrl = config('services.challan_pdf_api.url', 'https://api.invincibleocean.com/invincible/challan-pdf');
        $secretKey = config('services.challan_pdf_api.secret_key', 'ONsoB7pAonpp1FYJ0Bf6sHFuGLYGEbytHxURPsEmK64gt3HR8yDtxwDafwMuCaonL');
        $clientId = config('services.challan_pdf_api.client_id', '8f16f6344cbdcc74620cfdf7c87554f2:045cb470d3ae8da988c7e0982917e1ea');

        $apiRequest = [
            'challanNumber' => $vehicleNumber,
            'consent' => 'I explicitly consent to the collection, processing, and verification of my data for authentication, KYC, and compliance purposes.'
        ];

        try {
            $response = Http::withoutVerifying()
                ->timeout(60)
                ->withHeaders([
                    'secretKey' => $secretKey,
                    'clientId' => $clientId,
                    'Content-Type' => 'application/json',
                ])->post($apiUrl, $apiRequest);

            $apiResponse = $response->json();

            // Check if response is successful based on actual API logic
            $isSuccess = $response->successful() && isset($apiResponse['code']) && $apiResponse['code'] == 200 && !empty($apiResponse['pdfUrl']);

            // Save log first
            $searchLog = new ChallanPdfSearch();
            if ($userType === 'customer') {
                $searchLog->customer_id = $user->id;
            } else {
                $searchLog->dealer_id = $user->id;
            }
            $searchLog->vehicle_number = $vehicleNumber;
            $searchLog->is_success = $isSuccess;
            $searchLog->api_request = $apiRequest;
            $searchLog->api_response = $apiResponse;
            $searchLog->pdf_url = $isSuccess ? ($apiResponse['pdfUrl'] ?? null) : null;
            $searchLog->error_message = $isSuccess ? null : ($apiResponse['message'] ?? 'API Request Failed');

            if ($isSuccess) {
                // 5. Deduct amount ONLY if API response is successful
                $searchLog->charge_amount = $chargeAmount;
                
                // Deduct from wallet
                $user->wallet->balance -= $chargeAmount;
                $user->wallet->save();
                
                // Add wallet transaction record
                $user->wallet->transactions()->create([
                    'amount' => $chargeAmount,
                    'type' => 'debit',
                    'description' => "Challan PDF search for {$vehicleNumber}",
                    'reference_id' => null, // or add logic for reference
                ]);
            } else {
                $searchLog->charge_amount = 0; // No deduction on failure
            }

            $searchLog->save();

            return [
                'success' => $isSuccess,
                'message' => $isSuccess ? 'PDF Generated Successfully' : $searchLog->error_message,
                'pdf_url' => $searchLog->pdf_url,
                'search_log_id' => $searchLog->id,
            ];

        } catch (\Exception $e) {
            Log::error('Challan PDF API Error: ' . $e->getMessage());

            // Save Failed Log
            $searchLog = new ChallanPdfSearch();
            if ($userType === 'customer') {
                $searchLog->customer_id = $user->id;
            } else {
                $searchLog->dealer_id = $user->id;
            }
            $searchLog->vehicle_number = $vehicleNumber;
            $searchLog->is_success = false;
            $searchLog->api_request = $apiRequest;
            $searchLog->error_message = $e->getMessage();
            $searchLog->charge_amount = 0;
            $searchLog->save();

            return ['success' => false, 'message' => 'Something went wrong. Please try again.'];
        }
    }
}
