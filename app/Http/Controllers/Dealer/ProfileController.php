<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Dealer;

class ProfileController extends Controller
{
    public function edit()
    {
        $dealer = auth('dealer')->user();
        return view('dealer.profile.edit', compact('dealer'));
    }

    public function update(Request $request)
    {
        $dealer = auth('dealer')->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $data = [
            'name' => $request->name,
            'company_name' => $request->company_name
        ];

        if ($request->hasFile('profile_image')) {
            if ($dealer->profile_image) {
                Storage::disk('public')->delete($dealer->profile_image);
            }
            $data['profile_image'] = $request->file('profile_image')->store('dealers/profiles', 'public');
        }

        $dealer->update($data);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function sendPhoneOtp(Request $request)
    {
        $request->validate([
            'new_phone' => 'required|regex:/^[0-9]{10}$/',
        ]);

        $dealer = auth('dealer')->user();
        $oldPhone = $dealer->phone;
        $newPhone = $request->new_phone;

        if ($oldPhone === $newPhone) {
            return response()->json(['success' => false, 'message' => 'New phone number must be different from current.']);
        }

        // Check if new phone is already taken by another dealer
        if (Dealer::where('phone', $newPhone)->exists()) {
            return response()->json(['success' => false, 'message' => 'This phone number is already registered to another account.']);
        }

        $oldOtp = rand(100000, 999999);
        $newOtp = rand(100000, 999999);

        session([
            'profile_phone_update' => [
                'old_phone' => $oldPhone,
                'new_phone' => $newPhone,
                'old_otp' => $oldOtp,
                'new_otp' => $newOtp,
                'expires_at' => now()->addMinutes(10)
            ]
        ]);

        // Send OTP to OLD phone
        $this->sendSms($oldPhone, "Hi! Your verification code to change your registered number is {$oldOtp}. Valid for 10 mins. - Sars Infotech Pvt Ltd");
        
        // Send OTP to NEW phone
        $this->sendSms($newPhone, "Hi! Your verification code for confirming this new number is {$newOtp}. Valid for 10 mins. - Sars Infotech Pvt Ltd");

        return response()->json(['success' => true, 'message' => 'OTPs sent to both old and new phone numbers.']);
    }

    public function verifyPhoneOtp(Request $request)
    {
        $request->validate([
            'old_otp' => 'required|numeric|digits:6',
            'new_otp' => 'required|numeric|digits:6',
        ]);

        $sessionData = session('profile_phone_update');

        if (!$sessionData || now()->greaterThan($sessionData['expires_at'])) {
            return redirect()->back()->with('error', 'OTP session expired. Please request again.')->withInput();
        }

        if ((string)$sessionData['old_otp'] !== (string)$request->old_otp) {
            return redirect()->back()->with('error', 'Invalid OTP for the old phone number.')->withInput();
        }

        if ((string)$sessionData['new_otp'] !== (string)$request->new_otp) {
            return redirect()->back()->with('error', 'Invalid OTP for the new phone number.')->withInput();
        }

        $dealer = auth('dealer')->user();
        $dealer->update(['phone' => $sessionData['new_phone']]);

        session()->forget('profile_phone_update');

        return redirect()->back()->with('success', 'Phone number updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $dealer = auth('dealer')->user();

        if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $dealer->password)) {
            return back()->with('error', 'Current password does not match!');
        }

        $dealer->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->password)
        ]);

        return back()->with('success', 'Password updated successfully!');
    }

    private function sendSms($phone, $text)
    {
        $apiUrl = "https://pgapi.sparc.smartping.io/fe/api/v1/send";
        \Illuminate\Support\Facades\Http::get($apiUrl, [
            'username' => 'sarsinfo.trans',
            'password' => '6E5s8aI_',
            'unicode' => 'false',
            'from' => 'INSARS',
            'text' => $text,
            'to' => $phone,
            'dltContentId' => '1707177677498830200',
            'dltPrincipalEntityId' => '1701166126846262605'
        ]);
    }
}
