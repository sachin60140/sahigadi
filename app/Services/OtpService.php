<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OtpService
{
    /**
     * Send an OTP via the configured SMS gateway.
     *
     * @param string $phone
     * @param string $otp
     * @return bool
     */
    public function sendSms(string $phone, string $otp): bool
    {
        $text = "Hi! Your verification code is {$otp}. It is valid for 10 minutes. Please keep it confidential. - Sars Infotech Pvt Ltd";
        
        if (app()->environment('local')) {
            // Local development only: log the OTP and do not send an actual SMS
            Log::info("OTP for {$phone} is {$otp}");
            return true;
        }

        try {
            $apiUrl = config('services.smartping.api_url');
            
            $response = Http::get($apiUrl, [
                'username' => config('services.smartping.username'),
                'password' => config('services.smartping.password'),
                'unicode' => 'false',
                'from' => config('services.smartping.sender_id'),
                'text' => $text,
                'to' => $phone,
                'dltContentId' => config('services.smartping.dlt_content_id'),
                'dltPrincipalEntityId' => config('services.smartping.dlt_principal_id')
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("Failed to send OTP to {$phone}: " . $e->getMessage());
            return false;
        }
    }
}
