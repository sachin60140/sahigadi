<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Models\Dealer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('dealer.auth.login');
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
        return view('dealer.auth.register');
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

        $kycPath = $request->file('kyc_document')->store('dealers/kyc', 'public');
        $panPath = $request->file('pan_document')->store('dealers/pan', 'public');
        $gstPath = $request->hasFile('gst_document') ? $request->file('gst_document')->store('dealers/gst', 'public') : null;

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

        return redirect()->route('dealer.dashboard')->with('success', 'Registration successful! Your account is pending approval. Please complete your KYC verification.');
    }

    public function logout(Request $request)
    {
        auth('dealer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('dealer.login');
    }
}
