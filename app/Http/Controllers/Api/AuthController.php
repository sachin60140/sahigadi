<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use App\Models\Customer;
use App\Models\Dealer;

class AuthController extends Controller
{
    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|regex:/^[0-9]{10}$/',
            'type' => 'required|in:customer,dealer',
        ]);

        $phone = $request->phone;
        $type = $request->type;
        $ip = $request->ip();

        // Rate limit temporarily disabled for testing
        // if (RateLimiter::tooManyAttempts('api-send-otp:' . $ip, 3)) {
        //     $seconds = RateLimiter::availableIn('api-send-otp:' . $ip);
        //     return response()->json(['success' => false, 'message' => "Too many requests. Please try again in {$seconds} seconds."], 429);
        // }
        // RateLimiter::hit('api-send-otp:' . $ip, 600);

        if ($type === 'dealer') {
            $dealer = Dealer::where('phone', $phone)->first();
            if (!$dealer) {
                return response()->json(['success' => false, 'message' => 'No dealer found with this mobile number. Please register on the website.'], 404);
            }
        }

        $otp = rand(100000, 999999);
        
        Cache::put('api_otp_' . $type . '_' . $phone, $otp, now()->addMinutes(10));

        $apiUrl = "https://pgapi.sparc.smartping.io/fe/api/v1/send";
        $text = "Hi! Your verification code is {$otp}. It is valid for 10 minutes. Please keep it confidential. - Sars Infotech Pvt Ltd";

        try {
            Http::timeout(5)->get($apiUrl, [
                'username' => 'sarsinfo.trans',
                'password' => '6E5s8aI_',
                'unicode' => 'false',
                'from' => 'INSARS',
                'text' => $text,
                'to' => $phone,
                'dltContentId' => '1707177677498830200',
                'dltPrincipalEntityId' => '1701166126846262605'
            ]);
        } catch (\Exception $e) {
            \Log::error('SMS Provider Error: ' . $e->getMessage());
        }

        \Log::info("DEVELOPMENT OTP for {$phone}: {$otp}");

        return response()->json(['success' => true, 'message' => 'OTP sent successfully']);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|regex:/^[0-9]{10}$/',
            'otp' => 'required|numeric|digits:6',
            'type' => 'required|in:customer,dealer',
        ]);

        $phone = $request->phone;
        $otp = $request->otp;
        $type = $request->type;

        $cachedOtp = Cache::get('api_otp_' . $type . '_' . $phone);

        // Bypass OTP in local dev if needed, but we keep it strict for now.
        if (!$cachedOtp) {
            return response()->json(['success' => false, 'message' => 'OTP expired or not sent'], 400);
        }

        if ((string)$cachedOtp !== (string)$otp) {
            return response()->json(['success' => false, 'message' => 'Invalid OTP'], 400);
        }

        Cache::forget('api_otp_' . $type . '_' . $phone);

        if ($type === 'customer') {
            $user = Customer::firstOrCreate(['phone' => $phone]);
            
            // Calculate profile completion if 0
            if ($user->profile_completion_percentage === 0) {
                $user->calculateProfileCompletion();
            }
            
            $token = $user->createToken('customer_mobile_app', ['role:customer'])->plainTextToken;
            
            return response()->json([
                'success' => true,
                'token' => $token,
                'user' => $user,
                'type' => 'customer'
            ]);
        } else {
            $user = Dealer::where('phone', $phone)->first();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Dealer not found'], 404);
            }
            
            $token = $user->createToken('dealer_mobile_app', ['role:dealer'])->plainTextToken;
            
            return response()->json([
                'success' => true,
                'token' => $token,
                'user' => $user,
                'type' => 'dealer'
            ]);
        }
    }

    public function dealerLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $dealer = Dealer::where('email', $request->email)->first();

        if (!$dealer || !\Illuminate\Support\Facades\Hash::check($request->password, $dealer->password)) {
            return response()->json(['success' => false, 'message' => 'Invalid email or password'], 401);
        }

        $token = $dealer->createToken('dealer_mobile_app', ['role:dealer'])->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => $dealer,
            'type' => 'dealer'
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }
    
    public function user(Request $request)
    {
        return response()->json([
            'success' => true,
            'user' => $request->user()
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        if (!$user->currentAccessToken()->can('role:customer')) {
            return response()->json(['success' => false, 'message' => 'Only customers can update profile this way'], 403);
        }

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'pincode' => 'nullable|string|max:20',
            'gender' => 'nullable|string|in:Male,Female,Other',
        ]);

        $user->update($validated);
        $user->calculateProfileCompletion();
        $user->save(); // Save the recalculated percentage

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'user' => $user
        ]);
    }
}
