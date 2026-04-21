<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CustomerCarListing;
use App\Models\Dealer;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'pending_dealers' => Dealer::where('status', 'pending')->count(),
            'approved_dealers' => Dealer::where('status', 'approved')->count(),
            'total_dealers' => Dealer::count(),
            
            'pending_cars' => Car::where('status', 'pending')->count(),
            'approved_cars' => Car::where('status', 'approved')->count(),
            'total_cars' => Car::count(),
            
            'pending_customer_listings' => CustomerCarListing::where('status', 'pending')->count(),
            'approved_customer_listings' => CustomerCarListing::where('status', 'approved')->count(),
            'total_customer_listings' => CustomerCarListing::count(),
            
            'total_plans' => Plan::count(),
            
            'total_wallet_recharges' => \App\Models\WalletTransaction::where('type', 'credit')->sum('amount'),
            
            'total_enquiries' => \App\Models\Enquiry::count(),
            'contact_enquiries' => \App\Models\ContactEnquiry::where('is_read', false)->count(),
            
            'vahan_lookups' => \App\Models\VehicleDetail::count() + \App\Models\CustomerVehicleSearch::count() + \App\Models\AdminVehicleSearch::count(),
            'mahindra_lookups' => \App\Models\ServiceHistory::count() + \App\Models\CustomerServiceHistory::count() + \App\Models\AdminServiceHistory::count(),
            'maruti_lookups' => \App\Models\MarutiServiceHistory::count() + \App\Models\CustomerMarutiServiceHistory::count() + \App\Models\AdminMarutiServiceHistory::count(),
            'challan_lookups' => \App\Models\CustomerChallanSearch::count() + \App\Models\AdminChallanSearch::count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    public function login()
    {
        return view('admin.login');
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Invalid credentials')->withInput();
        }

        if (! $user->isAdmin()) {
            return back()->with('error', 'Access denied')->withInput();
        }

        auth('admin')->login($user);

        return redirect()->route('admin.dashboard')->with('success', 'Welcome back!');
    }

    public function logout()
    {
        auth('admin')->logout();

        return redirect()->route('admin.login')->with('success', 'Logged out successfully');
    }

    public function changePassword(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'current_password' => 'required',
                'new_password' => 'required|min:6|confirmed',
            ]);

            $user = auth('admin')->user();

            if (! Hash::check($request->current_password, $user->password)) {
                return back()->with('error', 'Current password is incorrect');
            }

            $user->update(['password' => Hash::make($request->new_password)]);

            return redirect()->route('admin.dashboard')->with('success', 'Password changed successfully');
        }

        return view('admin.change-password');
    }
}
