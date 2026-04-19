<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Services\SubscriptionService;
use App\Services\WalletService;

class DashboardController extends Controller
{
    public function __construct(
        protected WalletService $walletService,
        protected SubscriptionService $subscriptionService
    ) {}

    public function index()
    {
        $dealer = auth('dealer')->user();

        $stats = [
            'total_cars' => $dealer->cars()->count(),
            'approved_cars' => $dealer->cars()->where('status', 'approved')->count(),
            'pending_cars' => $dealer->cars()->where('status', 'pending')->count(),
            'new_enquiries' => $dealer->enquiries()->where('status', 'new')->count(),
            'wallet_balance' => $this->walletService->getBalance($dealer->id),
            'remaining_listings' => $this->subscriptionService->getRemainingListings($dealer),
        ];

        $recentEnquiries = $dealer->enquiries()
            ->with('car')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dealer.dashboard', compact('stats', 'recentEnquiries'));
    }
}
