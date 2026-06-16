<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Car;
use App\Models\CustomerCarListing;
use App\Models\Enquiry;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;

class EnquiryApiController extends Controller
{
    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|regex:/^[0-9]{10}$/',
            'car_id' => 'required',
            'is_customer_listing' => 'required|boolean',
        ]);

        $phone = $request->phone;
        $ip = $request->ip();

        // Rate limit: Max 3 OTPs per IP per 10 minutes
        if (RateLimiter::tooManyAttempts('send-otp:' . $ip, 3)) {
            $seconds = RateLimiter::availableIn('send-otp:' . $ip);
            return response()->json(['success' => false, 'message' => "Too many requests. Please try again in {$seconds} seconds."], 429);
        }
        RateLimiter::hit('send-otp:' . $ip, 600); // 10 minutes

        $otp = rand(100000, 999999);
        
        // Cache OTP for 5 minutes
        Cache::put('contact_otp_' . $phone, $otp, now()->addMinutes(10));

        $apiUrl = "https://pgapi.sparc.smartping.io/fe/api/v1/send";
        $text = "Hi! Your verification code is {$otp}. It is valid for 10 minutes. Please keep it confidential. - Sars Infotech Pvt Ltd";
        
        $response = Http::get($apiUrl, [
            'username' => config('services.smartping.username'),
            'password' => config('services.smartping.password'),
            'unicode' => 'false',
            'from' => 'INSARS',
            'text' => $text,
            'to' => $phone,
            'dltContentId' => '1707177677498830200',
            'dltPrincipalEntityId' => '1701166126846262605'
        ]);

        return response()->json(['success' => true, 'message' => 'OTP sent successfully']);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|regex:/^[0-9]{10}$/',
            'otp' => 'required|numeric|digits:6',
            'name' => 'required|string|max:255',
            'car_id' => 'required',
            'is_customer_listing' => 'required|boolean',
        ]);

        $phone = $request->phone;
        $otp = $request->otp;
        $carId = $request->car_id;
        $isCustomerListing = $request->is_customer_listing;

        $cachedOtp = Cache::get('contact_otp_' . $phone);

        if (!$cachedOtp) {
            return response()->json(['success' => false, 'message' => 'OTP expired or not sent']);
        }

        if ((string)$cachedOtp !== (string)$otp) {
            return response()->json(['success' => false, 'message' => 'Invalid OTP']);
        }

        // Verify successful
        Cache::forget('contact_otp_' . $phone);

        // Get actual contact number to return
        $contactNumber = '';
        $dealerId = null;

        if ($isCustomerListing) {
            $car = CustomerCarListing::find($carId);
            if ($car) {
                $contactNumber = $car->owner_phone;
            }
        } else {
            $car = Car::with('dealer')->find($carId);
            if ($car && $car->dealer) {
                $contactNumber = $car->dealer->phone;
                $dealerId = $car->dealer->id;
            }
        }

        if (!$contactNumber) {
            return response()->json(['success' => false, 'message' => 'Car or contact not found']);
        }

        // Prevent duplicate entries (allow once per car per day)
        $todayEnquiry = Enquiry::where('customer_phone', $phone)
            ->where('car_id', $carId)
            ->whereDate('created_at', now()->toDateString())
            ->first();

        if (!$todayEnquiry) {
            // Save Enquiry
            $enquiry = Enquiry::create([
                'car_id' => $carId,
                'dealer_id' => $dealerId,
                'customer_name' => $request->name,
                'customer_phone' => $phone,
                'message' => 'Contact details unlocked by customer.',
                'status' => 'new',
                'ip_address' => $request->ip(),
            ]);

            if ($dealerId && $car && $car->dealer && $car->dealer->email) {
                try {
                    \Illuminate\Support\Facades\Mail::to($car->dealer->email)
                        ->send(new \App\Mail\DealerNewEnquiryNotification($enquiry));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Failed to send dealer enquiry email: ' . $e->getMessage());
                }
            }
        }

        return response()->json([
            'success' => true, 
            'message' => 'Phone number verified successfully',
            'contact_number' => $contactNumber,
            'whatsapp_number' => preg_replace('/[^0-9]/', '', $contactNumber)
        ]);
    }
}
