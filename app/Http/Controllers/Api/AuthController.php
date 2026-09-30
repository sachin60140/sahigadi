<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
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

        if ($type === 'dealer') {
            $dealer = Dealer::where('phone', $phone)->first();
            if (!$dealer) {
                return response()->json(['success' => false, 'message' => 'No dealer found with this mobile number. Please register on the website.'], 404);
            }
        }

        $otp = random_int(100000, 999999);
        
        Cache::put('api_otp_' . $type . '_' . $phone, $otp, now()->addMinutes(10));

        $apiUrl = "https://pgapi.sparc.smartping.io/fe/api/v1/send";
        $text = "Hi! Your verification code is {$otp}. It is valid for 10 minutes. Please keep it confidential. - Sars Infotech Pvt Ltd";

        try {
            Http::timeout(5)->get($apiUrl, [
                'username' => config('services.smartping.username'),
                'password' => config('services.smartping.password'),
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

        // phone + OTP is the entire credential for verifyOtp(), so writing the
        // code to the log would let anyone who can read it sign in as that user.
        if (app()->environment('local')) {
            \Log::info("OTP for {$phone}: {$otp}");
        }

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
            // withTrashed matters: Customer soft-deletes now, so a plain
            // firstOrCreate would not see a deleted account and would create a
            // second row on the same phone number instead.
            $user = Customer::withTrashed()->where('phone', $phone)->first();

            if ($user && $user->trashed()) {
                if ($user->isRecoverable()) {
                    $user->restoreAccount();
                } else {
                    // Past the grace period the row is anonymised and its phone
                    // replaced, so this only happens if the window closed in
                    // between. Start fresh rather than hand back an emptied
                    // account.
                    $user = null;
                }
            }

            $user ??= Customer::create(['phone' => $phone]);
            
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

    /**
     * Delete the signed-in customer's account.
     *
     * Google Play requires an in-app route to this for any app that creates
     * accounts, alongside the public page at /account-deletion.
     *
     * The account is hidden everywhere and every token is revoked at once;
     * signing in with the same number within the grace period brings it back.
     * After that a scheduled pass anonymises it for good. The row itself
     * survives because invoices carry customer_id and a GST series cannot lose
     * its counterparty.
     */
    public function deleteAccount(Request $request)
    {
        $user = $request->user();

        // Dealers are onboarded and invoiced differently, and closing one would
        // strand their inventory and their wallet. Same shape of guard as
        // updateProfile.
        if (! $user->currentAccessToken()->can('role:customer')) {
            return response()->json([
                'success' => false,
                'message' => 'Dealer accounts are closed by contacting support.',
            ], 403);
        }

        // Typing the word is the confirmation. This is irreversible from the
        // user's point of view, so it should not be reachable by a stray tap
        // that got past the dialog.
        $request->validate([
            'confirm' => 'required|string|in:DELETE',
        ]);

        $user->requestDeletion();

        return response()->json([
            'success' => true,
            'message' => 'Your account has been deleted. Signing in with this number within '
                .Customer::GRACE_DAYS.' days will restore it.',
            'grace_days' => Customer::GRACE_DAYS,
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
