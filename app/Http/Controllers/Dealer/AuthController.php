<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Models\Dealer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return \Inertia\Inertia::render('Auth/DealerLogin');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (auth('dealer')->attempt($request->only('email', 'password'), $request->filled('remember'))) {
            $request->session()->regenerate();

            return redirect()->route('dealer.dashboard');
        }

        return back()->with('error', 'Invalid credentials')->withInput();
    }

    public function showRegister()
    {
        return \Inertia\Inertia::render('Dealer/Register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:dealers,email',
            'password' => 'required|min:8|confirmed',
            'phone' => 'required|string|max:20',
            'company_name' => 'nullable|string|max:255',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|string|max:20',
            'gst_number' => 'nullable|string|max:15',
            'kyc_document_number' => 'required|string|max:20',
            'kyc_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'pan_number' => 'required|string|max:20',
            'pan_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'gst_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if (session('dealer_phone_verified') !== $request->phone) {
            return redirect()->back()->withInput()->with('error', 'Please verify your phone number via OTP before completing registration.');
        }

        $kycPath = $request->file('kyc_document')->store('dealers/kyc', 'local');
        $panPath = $request->file('pan_document')->store('dealers/pan', 'local');
        $gstPath = $request->hasFile('gst_document') ? $request->file('gst_document')->store('dealers/gst', 'local') : null;

        $dealer = Dealer::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'company_name' => $request->company_name,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'pincode' => $request->pincode,
            'gst_number' => $request->gst_number,
            'kyc_document_type' => 'aadhar',
            'kyc_document_number' => $request->kyc_document_number,
            'kyc_document_path' => $kycPath,
            'pan_number' => $request->pan_number,
            'pan_document_path' => $panPath,
            'gst_document_path' => $gstPath,
            'status' => 'pending',
        ]);

        auth('dealer')->login($dealer);

        session()->forget('dealer_phone_verified');

        return redirect()->route('dealer.dashboard')->with('success', 'Registration successful! Your account is pending approval. Please complete your KYC verification.');
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|regex:/^[0-9]{10}$/',
        ]);

        $phone = $request->phone;
        $otp = random_int(100000, 999999);
        
        session(['dealer_otp_' . $phone => $otp]);
        session(['dealer_otp_time_' . $phone => now()]);

        $apiUrl = "https://pgapi.sparc.smartping.io/fe/api/v1/send";
        $text = "Hi! Your verification code is {$otp}. It is valid for 10 minutes. Please keep it confidential. - Sars Infotech Pvt Ltd";
        
        $response = \Illuminate\Support\Facades\Http::get($apiUrl, [
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
        ]);

        $phone = $request->phone;
        $otp = $request->otp;

        $sessionOtp = session('dealer_otp_' . $phone);
        $sessionTime = session('dealer_otp_time_' . $phone);

        if (!$sessionOtp || !$sessionTime) {
            return response()->json(['success' => false, 'message' => 'OTP expired or not sent']);
        }

        if (now()->diffInMinutes($sessionTime) > 10) {
            return response()->json(['success' => false, 'message' => 'OTP has expired']);
        }

        if ((string)$sessionOtp === (string)$otp) {
            session(['dealer_phone_verified' => $phone]);
            session()->forget('dealer_otp_' . $phone);
            session()->forget('dealer_otp_time_' . $phone);
            
            return response()->json(['success' => true, 'message' => 'Phone number verified successfully']);
        }

        return response()->json(['success' => false, 'message' => 'Invalid OTP']);
    }

    public function showForgotPassword()
    {
        return \Inertia\Inertia::render('Auth/DealerForgotPassword');
    }

    public function sendResetOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|regex:/^[0-9]{10}$/',
        ]);

        $phone = $request->phone;
        
        $dealer = Dealer::where('phone', $phone)->first();
        if (!$dealer) {
            return response()->json(['success' => false, 'message' => 'No dealer found with this phone number.']);
        }

        $otp = random_int(100000, 999999);
        
        session(['dealer_reset_otp_' . $phone => $otp]);
        session(['dealer_reset_otp_time_' . $phone => now()]);

        $apiUrl = "https://pgapi.sparc.smartping.io/fe/api/v1/send";
        $text = "Hi! Your verification code is {$otp}. It is valid for 10 minutes. Please keep it confidential. - Sars Infotech Pvt Ltd";
        
        $response = \Illuminate\Support\Facades\Http::get($apiUrl, [
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

    public function resetPassword(Request $request)
    {
        $request->validate([
            'phone' => 'required|regex:/^[0-9]{10}$/',
            'otp' => 'required|numeric|digits:6',
            'password' => 'required|min:8|confirmed'
        ]);

        $phone = $request->phone;
        $otp = $request->otp;

        $sessionOtp = session('dealer_reset_otp_' . $phone);
        $sessionTime = session('dealer_reset_otp_time_' . $phone);

        if (!$sessionOtp || !$sessionTime) {
            return redirect()->back()->with('error', 'OTP expired or not sent.')->withInput();
        }

        if (now()->diffInMinutes($sessionTime) > 10) {
            return redirect()->back()->with('error', 'OTP has expired.')->withInput();
        }

        if ((string)$sessionOtp !== (string)$otp) {
            return redirect()->back()->with('error', 'Invalid OTP.')->withInput();
        }

        $dealer = Dealer::where('phone', $phone)->first();
        if (!$dealer) {
            return redirect()->back()->with('error', 'Dealer not found.')->withInput();
        }

        $dealer->update([
            'password' => Hash::make($request->password)
        ]);

        session()->forget('dealer_reset_otp_' . $phone);
        session()->forget('dealer_reset_otp_time_' . $phone);

        return redirect()->route('dealer.login')->with('success', 'Password reset successfully! You can now login.');
    }

    public function logout(Request $request)
    {
        auth('dealer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('dealer.login');
    }
}
