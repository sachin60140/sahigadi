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
use Inertia\Inertia;

class DealerController extends Controller
{
    public function index(Request $request)
    {
        $query = Dealer::with('wallet')->withCount('cars');

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('dealer_unique_id', 'like', '%'.$request->search.'%')
                    ->orWhere('name', 'like', '%'.$request->search.'%')
                    ->orWhere('email', 'like', '%'.$request->search.'%')
                    ->orWhere('phone', 'like', '%'.$request->search.'%');
            });
        }

        $dealers = $query->orderBy('created_at', 'desc')->paginate(20);

        return Inertia::render('Admin/Dealers/Index', [
            'dealers' => $dealers->through(fn ($dealer) => $this->mapDealerList($dealer)),
            'filters' => [
                'status' => $request->query('status', 'all'),
                'search' => $request->query('search', ''),
            ],
            'stats' => [
                'total' => Dealer::count(),
                'approved' => Dealer::where('status', 'approved')->count(),
                'pending' => Dealer::where('status', 'pending')->count(),
                'rejected' => Dealer::where('status', 'rejected')->count(),
            ],
        ]);
    }

    public function create()
    {
        $plans = Plan::orderBy('price')->get();

        return Inertia::render('Admin/Dealers/Create', [
            'plans' => $plans->map(fn (Plan $plan) => $this->mapPlan($plan))->values(),
            'actions' => [
                'store' => route('admin.dealers.store'),
                'back' => route('admin.dealers.index'),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:dealers,email',
            'password' => 'required|min:6',
            'phone' => 'nullable|string|max:20',
            'company_name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:20',
            'status' => 'required|in:pending,approved,rejected',
            'gst_number' => 'nullable|string|max:15',
            'kyc_document_type' => 'nullable|in:aadhar,pan,voter_id,driving_license',
            'kyc_document_number' => 'nullable|string|max:20',
            'kyc_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'pan_number' => 'nullable|string|max:20',
            'pan_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'gst_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'assign_plan' => 'nullable|exists:plans,id',
        ]);

        $kycPath = $request->hasFile('kyc_document') ? $request->file('kyc_document')->store('dealers/kyc', 'local') : null;
        $panPath = $request->hasFile('pan_document') ? $request->file('pan_document')->store('dealers/pan', 'local') : null;
        $gstPath = $request->hasFile('gst_document') ? $request->file('gst_document')->store('dealers/gst', 'local') : null;

        $dealer = Dealer::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'company_name' => $validated['company_name'] ?? null,
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
            'state' => $validated['state'] ?? null,
            'pincode' => $validated['pincode'] ?? null,
            'gst_number' => $validated['gst_number'] ?? null,
            'kyc_document_type' => $validated['kyc_document_type'] ?? 'aadhar',
            'kyc_document_number' => $validated['kyc_document_number'] ?? null,
            'kyc_document_path' => $kycPath,
            'pan_number' => $validated['pan_number'] ?? null,
            'pan_document_path' => $panPath,
            'gst_document_path' => $gstPath,
            'status' => $validated['status'],
        ]);

        Wallet::firstOrCreate(['dealer_id' => $dealer->id], ['balance' => 0]);

        if (! empty($validated['assign_plan'])) {
            $plan = Plan::find($validated['assign_plan']);
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

        return Inertia::render('Admin/Dealers/Edit', [
            'dealer' => $this->mapDealerForm($dealer),
            'plans' => $plans->map(fn (Plan $plan) => $this->mapPlan($plan))->values(),
            'actions' => [
                'update' => route('admin.dealers.update', $dealer),
                'back' => route('admin.dealers.show', $dealer),
            ],
        ]);
    }

    public function update(Request $request, Dealer $dealer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:dealers,email,'.$dealer->id,
            'password' => 'nullable|min:6',
            'phone' => 'nullable|string|max:20',
            'company_name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:20',
            'status' => 'required|in:pending,approved,rejected',
            'gst_number' => 'nullable|string|max:15',
            'kyc_document_type' => 'nullable|in:aadhar,pan,voter_id,driving_license',
            'kyc_document_number' => 'nullable|string|max:20',
            'kyc_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'pan_number' => 'nullable|string|max:20',
            'pan_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'gst_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'company_name' => $validated['company_name'] ?? null,
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
            'state' => $validated['state'] ?? null,
            'pincode' => $validated['pincode'] ?? null,
            'gst_number' => $validated['gst_number'] ?? null,
            'kyc_document_type' => $validated['kyc_document_type'] ?? 'aadhar',
            'kyc_document_number' => $validated['kyc_document_number'] ?? null,
            'pan_number' => $validated['pan_number'] ?? null,
            'status' => $validated['status'],
        ];

        if (! empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        if ($request->hasFile('kyc_document')) {
            $data['kyc_document_path'] = $request->file('kyc_document')->store('dealers/kyc', 'local');
        }

        if ($request->hasFile('pan_document')) {
            $data['pan_document_path'] = $request->file('pan_document')->store('dealers/pan', 'local');
        }

        if ($request->hasFile('gst_document')) {
            $data['gst_document_path'] = $request->file('gst_document')->store('dealers/gst', 'local');
        }

        $dealer->update($data);

        return redirect()->route('admin.dealers.show', $dealer)->with('success', 'Dealer updated successfully!');
    }

    public function show(Dealer $dealer)
    {
        $dealer->load([
            'wallet',
            'cars' => fn ($query) => $query->latest()->limit(30),
            'subscriptions.plan',
        ]);

        $walletTransactions = collect();
        if ($dealer->wallet) {
            $walletTransactions = $dealer->wallet->transactions()->orderBy('created_at', 'desc')->limit(20)->get();
        }

        $plans = Plan::orderBy('price')->get();

        return Inertia::render('Admin/Dealers/Show', [
            'dealer' => $this->mapDealerDetail($dealer),
            'walletTransactions' => $walletTransactions->map(fn ($transaction) => $this->mapWalletTransaction($transaction))->values(),
            'plans' => $plans->map(fn ($plan) => $this->mapPlan($plan))->values(),
        ]);
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

    public function toggleStatus(Dealer $dealer)
    {
        if ($dealer->status === 'approved') {
            $dealer->update([
                'status' => 'rejected',
                'rejection_reason' => 'Deactivated by administrator.'
            ]);
            return back()->with('success', 'Dealer deactivated successfully');
        } else {
            $dealer->update(['status' => 'approved']);
            Wallet::firstOrCreate(['dealer_id' => $dealer->id], ['balance' => 0]);
            return back()->with('success', 'Dealer activated successfully');
        }
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

    private function mapDealerList(Dealer $dealer): array
    {
        return [
            'id' => $dealer->id,
            'dealer_unique_id' => $dealer->dealer_unique_id,
            'name' => $dealer->name,
            'email' => $dealer->email,
            'phone' => $dealer->phone,
            'company_name' => $dealer->company_name,
            'city' => $dealer->city,
            'state' => $dealer->state,
            'status' => $dealer->status,
            'wallet_balance' => (float) ($dealer->wallet?->balance ?? 0),
            'cars_count' => (int) ($dealer->cars_count ?? 0),
            'gst_number' => $dealer->gst_number,
            'gst_verified' => (bool) $dealer->gst_verified,
            'joined_at' => $this->formatDate($dealer->created_at),
            'show_url' => route('admin.dealers.show', $dealer),
            'edit_url' => route('admin.dealers.edit', $dealer),
            'toggle_status_url' => route('admin.dealers.toggle-status', $dealer),
        ];
    }

    private function mapDealerDetail(Dealer $dealer): array
    {
        return [
            ...$this->mapDealerList($dealer),
            'address' => $dealer->address,
            'pincode' => $dealer->pincode,
            'rejection_reason' => $dealer->rejection_reason,
            'kyc_document_type' => $dealer->kyc_document_type ?: 'aadhar',
            'kyc_document_number' => $dealer->kyc_document_number,
            'pan_number' => $dealer->pan_number,
            'gst_verified_at' => $this->formatDate($dealer->gst_verified_at),
            'profile_completion' => $dealer->calculateProfileCompletion(),
            'missing_profile_fields' => $dealer->getMissingProfileFields(),
            'documents' => [
                'kyc' => $dealer->kyc_document_path ? route('admin.dealers.document', ['dealer' => $dealer, 'type' => 'kyc']) : null,
                'pan' => $dealer->pan_document_path ? route('admin.dealers.document', ['dealer' => $dealer, 'type' => 'pan']) : null,
                'gst' => $dealer->gst_document_path ? route('admin.dealers.document', ['dealer' => $dealer, 'type' => 'gst']) : null,
            ],
            'cars' => $dealer->cars->map(fn ($car) => [
                'id' => $car->id,
                'title' => $car->title,
                'price' => (float) ($car->price ?? 0),
                'status' => $car->status,
                'city' => $car->city,
                'year' => $car->year,
                'fuel_type' => $car->fuel_type,
                'transmission' => $car->transmission,
            ])->values(),
            'subscriptions' => $dealer->subscriptions->map(fn ($subscription) => [
                'id' => $subscription->id,
                'plan_name' => $subscription->plan?->name ?? 'Unknown plan',
                'listing_limit' => (int) ($subscription->plan?->listing_limit ?? 0),
                'listings_used' => (int) $subscription->listings_used,
                'active_listings' => $subscription->getActiveListingsCount(),
                'expires_at' => $this->formatDate($subscription->expires_at),
                'is_active' => $subscription->isActive(),
            ])->values(),
            'actions' => [
                'back' => route('admin.dealers.index'),
                'edit' => route('admin.dealers.edit', $dealer),
                'approve' => route('admin.dealers.approve', $dealer),
                'reject' => route('admin.dealers.reject', $dealer),
                'toggle_status' => route('admin.dealers.toggle-status', $dealer),
                'add_money' => route('admin.dealers.add-money', $dealer),
                'debit_money' => route('admin.dealers.debit-money', $dealer),
                'assign_plan' => route('admin.dealers.assign-plan', $dealer),
                'verify_gst' => route('admin.dealers.verify-gst', $dealer),
                'unverify_gst' => route('admin.dealers.unverify-gst', $dealer),
            ],
        ];
    }

    private function mapDealerForm(Dealer $dealer): array
    {
        return [
            'id' => $dealer->id,
            'dealer_unique_id' => $dealer->dealer_unique_id,
            'name' => $dealer->name,
            'email' => $dealer->email,
            'phone' => $dealer->phone,
            'company_name' => $dealer->company_name,
            'address' => $dealer->address,
            'city' => $dealer->city,
            'state' => $dealer->state,
            'pincode' => $dealer->pincode,
            'status' => $dealer->status,
            'gst_number' => $dealer->gst_number,
            'gst_verified' => (bool) $dealer->gst_verified,
            'kyc_document_type' => $dealer->kyc_document_type ?: 'aadhar',
            'kyc_document_number' => $dealer->kyc_document_number,
            'pan_number' => $dealer->pan_number,
            'profile_completion' => $dealer->calculateProfileCompletion(),
            'documents' => [
                'kyc' => $dealer->kyc_document_path ? route('admin.dealers.document', ['dealer' => $dealer, 'type' => 'kyc']) : null,
                'pan' => $dealer->pan_document_path ? route('admin.dealers.document', ['dealer' => $dealer, 'type' => 'pan']) : null,
                'gst' => $dealer->gst_document_path ? route('admin.dealers.document', ['dealer' => $dealer, 'type' => 'gst']) : null,
            ],
        ];
    }

    private function mapWalletTransaction($transaction): array
    {
        return [
            'id' => $transaction->id,
            'type' => $transaction->type,
            'amount' => (float) $transaction->amount,
            'remark' => $transaction->remark,
            'reference_id' => $transaction->reference_id,
            'reference_type' => $transaction->reference_type,
            'created_at' => $this->formatDateTime($transaction->created_at),
        ];
    }

    private function mapPlan(Plan $plan): array
    {
        return [
            'id' => $plan->id,
            'name' => $plan->name,
            'price' => (float) $plan->price,
            'listing_limit' => (int) $plan->listing_limit,
            'duration_days' => (int) $plan->duration_days,
            'is_active' => (bool) $plan->is_active,
        ];
    }

    public function document(Dealer $dealer, string $type)
    {
        $paths = [
            'kyc' => $dealer->kyc_document_path,
            'pan' => $dealer->pan_document_path,
            'gst' => $dealer->gst_document_path,
        ];

        abort_unless(array_key_exists($type, $paths), 404);

        $path = $paths[$type];
        abort_if(! $path || ! \Illuminate\Support\Facades\Storage::disk('local')->exists($path), 404);

        return \Illuminate\Support\Facades\Storage::disk('local')->response($path);
    }

    private function documentUrl(?string $path): ?string
    {
        return $path ? asset('storage/'.$path) : null;
    }

    private function formatDate($value): ?string
    {
        return $value ? $value->format('d M Y') : null;
    }

    private function formatDateTime($value): ?string
    {
        return $value ? $value->format('d M Y, h:i A') : null;
    }
}
