<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CustomerCarListing;
use App\Models\ContactUnlockLog;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class ContactUnlockController extends Controller
{
    protected OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'viewer_name' => 'required|string|max:255',
            'viewer_mobile' => 'required|numeric|digits:10',
            'car_id' => 'nullable|integer',
            'customer_car_listing_id' => 'nullable|integer',
            'source_page' => 'nullable|string',
        ]);

        if (!$request->car_id && !$request->customer_car_listing_id) {
            return response()->json(['success' => false, 'message' => 'Car ID is required.']);
        }

        $type = $request->car_id ? Car::class : CustomerCarListing::class;
        $id = $request->car_id ?? $request->customer_car_listing_id;
        $viewerMobile = $request->viewer_mobile;
        $ipAddress = $request->ip();

        // Rate Limiter: 3 attempts per 10 minutes per mobile + IP + car
        $rateLimitKey = 'send_contact_otp:' . $viewerMobile . ':' . $ipAddress . ':' . $type . ':' . $id;
        if (RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            return response()->json(['success' => false, 'message' => "Too many attempts. Please try again in {$seconds} seconds."]);
        }
        RateLimiter::hit($rateLimitKey, 600); // 10 minutes

        $item = $type::find($id);
        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Car not found.']);
        }

        // Check if log already exists
        $log = ContactUnlockLog::where('unlockable_type', $type)
            ->where('unlockable_id', $id)
            ->where('viewer_mobile', $viewerMobile)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$log) {
            $log = ContactUnlockLog::create([
                'unlockable_type' => $type,
                'unlockable_id' => $id,
                'viewer_name' => $request->viewer_name,
                'viewer_mobile' => $viewerMobile,
                'ip_address' => $ipAddress,
                'user_agent' => $request->userAgent(),
                'source_page' => $request->source_page,
                'status' => 'pending',
                'otp_attempts' => 0,
                'resend_count' => 0,
            ]);
        } else {
            $log->resend_count += 1;
        }

        // Generate and send OTP
        $otp = random_int(100000, 999999);
        
        // Store securely in session for verify
        $sessionKey = 'contact_otp_' . $log->id;
        Session::put($sessionKey, [
            'hash' => Hash::make($otp),
            'expires_at' => now()->addMinutes(10)
        ]);

        $log->otp_sent_at = now();
        $log->status = 'otp_sent';
        $log->save();

        $success = $this->otpService->sendSms($viewerMobile, $otp);

        if ($success) {
            return response()->json([
                'success' => true, 
                'message' => 'OTP sent successfully.',
                'log_id' => $log->id
            ]);
        }

        $log->status = 'failed';
        $log->save();
        return response()->json(['success' => false, 'message' => 'Failed to send OTP. Please try again.']);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'log_id' => 'required|integer|exists:contact_unlock_logs,id',
            'otp' => 'required|numeric|digits:6',
        ]);

        $log = ContactUnlockLog::find($request->log_id);

        // Rate Limiter: 5 attempts per 10 minutes per mobile + IP + log_id
        $rateLimitKey = 'verify_contact_otp:' . $log->viewer_mobile . ':' . $request->ip() . ':' . $log->id;
        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            $log->status = 'rate_limited';
            $log->save();
            return response()->json(['success' => false, 'message' => "Too many attempts. Please try again in {$seconds} seconds."]);
        }
        RateLimiter::hit($rateLimitKey, 600); // 10 minutes

        $sessionKey = 'contact_otp_' . $log->id;
        $sessionData = Session::get($sessionKey);

        $log->otp_attempts += 1;
        $log->save();

        if (!$sessionData) {
            return response()->json(['success' => false, 'message' => 'OTP session expired or not found. Please request a new OTP.']);
        }

        if (now()->greaterThan($sessionData['expires_at'])) {
            $log->status = 'expired';
            $log->save();
            Session::forget($sessionKey);
            return response()->json(['success' => false, 'message' => 'OTP has expired. Please request a new one.']);
        }

        if (Hash::check($request->otp, $sessionData['hash'])) {
            // Success
            $log->status = 'verified';
            $log->otp_verified_at = now();
            $log->contact_revealed_at = now();
            
            // Get contact number
            $contactNumber = null;
            if ($log->unlockable_type === Car::class) {
                $item = Car::with('dealer')->find($log->unlockable_id);
                $contactNumber = $item->dealer->phone ?? null;
            } else {
                $item = CustomerCarListing::with('customer')->find($log->unlockable_id);
                $contactNumber = $item->customer->phone ?? $item->phone ?? null;
            }

            if ($contactNumber) {
                $log->revealed_contact_last4 = substr($contactNumber, -4);
            }
            $log->save();
            Session::forget($sessionKey);
            RateLimiter::clear($rateLimitKey);

            return response()->json([
                'success' => true,
                'message' => 'OTP verified successfully.',
                'contact_number' => $contactNumber
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Invalid OTP.']);
    }
}
