<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dealer;
use App\Models\Plan;
use App\Models\Wallet;
use App\Services\SubscriptionService;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DealerController extends Controller
{
    public function index(Request $request)
    {
        $query = Dealer::query();

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('email', 'like', '%'.$request->search.'%')
                    ->orWhere('phone', 'like', '%'.$request->search.'%');
            });
        }

        $dealers = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.dealers.index', compact('dealers'));
    }

    public function create()
    {
        $plans = Plan::orderBy('price')->get();

        return view('admin.dealers.create', compact('plans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:dealers,email',
            'password' => 'required|min:6',
            'phone' => 'nullable|string|max:20',
            'company_name' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'status' => 'required|in:pending,approved,rejected',
            'gst_number' => 'nullable|string|max:15',
            'kyc_document_type' => 'nullable|in:aadhar,pan,voter_id,driving_license',
            'kyc_document_number' => 'nullable|string|max:20',
            'kyc_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'gst_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'assign_plan' => 'nullable|exists:plans,id',
        ]);

        $kycPath = $request->hasFile('kyc_document') ? $request->file('kyc_document')->store('dealers/kyc', 'public') : null;
        $gstPath = $request->hasFile('gst_document') ? $request->file('gst_document')->store('dealers/gst', 'public') : null;

        $dealer = Dealer::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'company_name' => $request->company_name,
            'city' => $request->city,
            'gst_number' => $request->gst_number,
            'kyc_document_type' => $request->kyc_document_type,
            'kyc_document_number' => $request->kyc_document_number,
            'kyc_document_path' => $kycPath,
            'gst_document_path' => $gstPath,
            'status' => $request->status,
        ]);

        Wallet::firstOrCreate(['dealer_id' => $dealer->id], ['balance' => 0]);

        if ($request->assign_plan) {
            $plan = Plan::find($request->assign_plan);
            if ($plan) {
                $subscriptionService = app(SubscriptionService::class);
                $subscriptionService->purchasePlan($dealer, $plan);
            }
        }

        return redirect()->route('admin.dealers.show', $dealer)->with('success', 'Dealer created successfully!');
    }

    public function edit(Dealer $dealer)
    {
        $plans = Plan::orderBy('price')->get();

        return view('admin.dealers.edit', compact('dealer', 'plans'));
    }

    public function update(Request $request, Dealer $dealer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:dealers,email,'.$dealer->id,
            'password' => 'nullable|min:6',
            'phone' => 'nullable|string|max:20',
            'company_name' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'status' => 'required|in:pending,approved,rejected',
            'gst_number' => 'nullable|string|max:15',
            'kyc_document_type' => 'nullable|in:aadhar,pan,voter_id,driving_license',
            'kyc_document_number' => 'nullable|string|max:20',
            'kyc_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'gst_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'company_name' => $request->company_name,
            'city' => $request->city,
            'gst_number' => $request->gst_number,
            'kyc_document_type' => $request->kyc_document_type,
            'kyc_document_number' => $request->kyc_document_number,
            'status' => $request->status,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('kyc_document')) {
            $data['kyc_document_path'] = $request->file('kyc_document')->store('dealers/kyc', 'public');
        }

        if ($request->hasFile('gst_document')) {
            $data['gst_document_path'] = $request->file('gst_document')->store('dealers/gst', 'public');
        }

        $dealer->update($data);

        return redirect()->route('admin.dealers.show', $dealer)->with('success', 'Dealer updated successfully!');
    }

    public function show(Dealer $dealer)
    {
        $dealer->load(['wallet', 'cars', 'subscriptions.plan']);

        $walletTransactions = collect();
        if ($dealer->wallet) {
            $walletTransactions = $dealer->wallet->transactions()->orderBy('created_at', 'desc')->limit(20)->get();
        }

        $plans = Plan::orderBy('price')->get();

        return view('admin.dealers.show', compact('dealer', 'walletTransactions', 'plans'));
    }

    public function approve(Dealer $dealer)
    {
        $dealer->update(['status' => 'approved']);

        $wallet = Wallet::firstOrCreate(['dealer_id' => $dealer->id], ['balance' => 0]);

        return back()->with('success', 'Dealer approved successfully');
    }

    public function reject(Request $request, Dealer $dealer)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $dealer->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', 'Dealer rejected');
    }

    public function addMoney(Request $request, Dealer $dealer)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'remark' => 'nullable|string|max:255',
        ]);

        $walletService = app(WalletService::class);
        $walletService->credit(
            $dealer->id,
            $request->amount,
            $request->remark ?? 'Amount added by admin',
            'admin_'.time(),
            'admin_credit'
        );

        return back()->with('success', 'Amount added to wallet successfully');
    }

    public function debitMoney(Request $request, Dealer $dealer)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'remark' => 'nullable|string|max:255',
        ]);

        $walletService = app(WalletService::class);
        $balance = $walletService->getBalance($dealer->id);

        if ($balance < $request->amount) {
            return back()->with('error', 'Insufficient balance. Current balance: ₹'.number_format($balance, 2));
        }

        $walletService->debit(
            $dealer->id,
            $request->amount,
            $request->remark ?? 'Amount debited by admin',
            'admin_debit_'.time(),
            'admin_debit'
        );

        return back()->with('success', 'Amount debited from wallet successfully');
    }

    public function assignPlan(Request $request, Dealer $dealer)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);

        $plan = Plan::find($request->plan_id);
        $subscriptionService = app(SubscriptionService::class);
        $subscriptionService->purchasePlan($dealer, $plan);

        return back()->with('success', 'Plan assigned successfully!');
    }

    public function verifyGst(Dealer $dealer)
    {
        $dealer->update([
            'gst_verified' => true,
            'gst_verified_at' => now(),
        ]);

        return back()->with('success', 'GST verified successfully');
    }

    public function unverifyGst(Dealer $dealer)
    {
        $dealer->update([
            'gst_verified' => false,
            'gst_verified_at' => null,
        ]);

        return back()->with('success', 'GST verification removed');
    }
}
