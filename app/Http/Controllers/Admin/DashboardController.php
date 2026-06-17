<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\ContactEnquiry;
use App\Models\CustomerCarListing;
use App\Models\Dealer;
use App\Models\Enquiry;
use App\Models\Payment;
use App\Models\PaymentLink;
use App\Models\Plan;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $paymentTotal = Payment::where('status', 'completed')->sum('amount');
        $paymentMonth = Payment::where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(30))
            ->sum('amount');
        $paymentToday = Payment::where('status', 'completed')
            ->whereDate('created_at', today())
            ->sum('amount');
        $gatewaySummary = Payment::selectRaw("COALESCE(payment_gateway, 'unknown') as gateway, COUNT(*) as count, COALESCE(SUM(amount), 0) as amount")
            ->where('status', 'completed')
            ->groupBy('payment_gateway')
            ->get()
            ->map(fn ($row) => [
                'gateway' => ucfirst((string) $row->gateway),
                'count' => (int) $row->count,
                'amount' => (float) $row->amount,
            ])
            ->values();

        $pendingDealers = Dealer::where('status', 'pending')->count();
        $pendingCars = Car::where('status', 'pending')->count();
        $pendingCustomerListings = CustomerCarListing::where('status', 'pending')->count();
        $unreadContactEnquiries = ContactEnquiry::where('is_read', false)->count();

        $stats = [
            'pending_dealers' => $pendingDealers,
            'approved_dealers' => Dealer::where('status', 'approved')->count(),
            'total_dealers' => Dealer::count(),
            
            'pending_cars' => $pendingCars,
            'approved_cars' => Car::where('status', 'approved')->count(),
            'total_cars' => Car::count(),
            
            'pending_customer_listings' => $pendingCustomerListings,
            'approved_customer_listings' => CustomerCarListing::where('status', 'approved')->count(),
            'total_customer_listings' => CustomerCarListing::count(),
            
            'total_plans' => Plan::count(),
            
            'total_wallet_recharges' => \App\Models\WalletTransaction::where('type', 'credit')->sum('amount'),
            
            'dealer_wallet_balance' => \App\Models\Wallet::sum('balance'),
            'customer_wallet_balance' => \App\Models\CustomerWallet::sum('balance'),
            
            'total_customers' => \App\Models\Customer::count(),
            'today_customers' => \App\Models\Customer::whereDate('created_at', today())->count(),
            'today_dealers' => Dealer::whereDate('created_at', today())->count(),
            
            'total_enquiries' => Enquiry::count(),
            'contact_enquiries' => $unreadContactEnquiries,
            
            'vahan_lookups' => \App\Models\VehicleDetail::count() + \App\Models\CustomerVehicleSearch::count() + \App\Models\AdminVehicleSearch::count(),
            'mahindra_lookups' => \App\Models\ServiceHistory::count() + \App\Models\CustomerServiceHistory::count() + \App\Models\AdminServiceHistory::count(),
            'maruti_lookups' => \App\Models\MarutiServiceHistory::count() + \App\Models\CustomerMarutiServiceHistory::count() + \App\Models\AdminMarutiServiceHistory::count(),
            'challan_lookups' => \App\Models\CustomerChallanSearch::count() + \App\Models\AdminChallanSearch::count(),
            'payment_total' => (float) $paymentTotal,
            'payment_month' => (float) $paymentMonth,
            'payment_today' => (float) $paymentToday,
            'pending_actions' => $pendingDealers + $pendingCars + $pendingCustomerListings + $unreadContactEnquiries,
        ];

        $paymentLinks = [
            'total' => PaymentLink::count(),
            'pending' => PaymentLink::where('status', 'pending')->count(),
            'paid' => PaymentLink::where('status', 'paid')->count(),
            'expired' => PaymentLink::where('status', 'pending')->where('expires_at', '<', now())->count(),
            'pending_amount' => (float) PaymentLink::where('status', 'pending')->sum('amount'),
        ];

        $recentPayments = Payment::with(['dealer:id,name,email', 'customer:id,name,email'])
            ->latest()
            ->take(6)
            ->get()
            ->map(fn (Payment $payment) => [
                'id' => $payment->id,
                'gateway' => $payment->payment_gateway ?: 'unknown',
                'amount' => (float) $payment->amount,
                'status' => $payment->status,
                'type' => $payment->type,
                'party' => $payment->dealer?->name ?: $payment->customer?->name ?: 'Direct payment',
                'created_at' => optional($payment->created_at)->format('d M, h:i A'),
            ]);

        $trendStart = now()->subDays(29)->startOfDay();
        $trendRows = Payment::where('status', 'completed')
            ->where('created_at', '>=', $trendStart)
            ->selectRaw('DATE(created_at) as day, COALESCE(SUM(amount), 0) as total')
            ->groupBy('day')
            ->pluck('total', 'day');

        $revenueTrend = collect(range(0, 29))->map(function ($offset) use ($trendStart, $trendRows) {
            $day = $trendStart->copy()->addDays($offset);
            $key = $day->format('Y-m-d');

            return [
                'date' => $day->format('d M'),
                'amount' => (float) ($trendRows[$key] ?? 0),
            ];
        })->values();

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'revenueTrend' => $revenueTrend,
            'gatewaySummary' => $gatewaySummary,
            'paymentLinks' => $paymentLinks,
            'recentPayments' => $recentPayments,
            'gatewayHealth' => [
                'razorpay_active' => Setting::isRazorpayActive(),
                'phonepe_active' => Setting::isPhonePeActive(),
                'phonepe_environment' => Setting::getPhonePeEnvironment(),
                'dealer_min_recharge' => Setting::getMinimumWalletRechargeAmount(),
                'customer_min_recharge' => Setting::getCustomerMinimumWalletRechargeAmount(),
            ],
            'generatedAt' => now()->format('l, d M Y h:i A'),
        ]);
    }

    public function login()
    {
        return Inertia::render('Admin/Auth/Login', [
            'actions' => [
                'authenticate' => route('admin.authenticate'),
                'home' => route('home'),
            ],
        ]);
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
        $request->session()->regenerate();

        return redirect()->route('admin.dashboard')->with('success', 'Welcome back!');
    }

    public function logout(Request $request)
    {
        auth('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Logged out successfully');
    }

    public function changePassword(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'current_password' => 'required',
                'new_password' => 'required|min:8|confirmed',
            ]);

            $user = auth('admin')->user();

            if (! Hash::check($request->current_password, $user->password)) {
                return back()->with('error', 'Current password is incorrect');
            }

            $user->update(['password' => Hash::make($request->new_password)]);

            return redirect()->route('admin.dashboard')->with('success', 'Password changed successfully');
        }

        return Inertia::render('Admin/Auth/ChangePassword', [
            'actions' => [
                'update' => route('admin.change-password'),
                'cancel' => route('admin.dashboard'),
            ],
        ]);
    }
}
