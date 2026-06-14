<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Services\SubscriptionService;
use App\Services\WalletService;
use Inertia\Inertia;

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
            'active_cars' => $dealer->cars()->where('is_active', true)->where('status', 'approved')->count(),
            'active_pending_cars' => $dealer->cars()->where('is_active', true)->where('status', 'pending')->count(),
            'new_enquiries' => $dealer->enquiries()->where('status', 'new')->count(),
            'wallet_balance' => $this->walletService->getBalance($dealer->id),
            'remaining_listings' => $this->subscriptionService->getRemainingListings($dealer),
        ];

        $recentEnquiries = $dealer->enquiries()
            ->with('car')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return Inertia::render('Dealer/Dashboard', [
            'stats' => $stats,
            'dealer' => [
                'first_name' => strtok($dealer->name, ' '),
                'status' => $dealer->status,
                'catalog_url' => $dealer->catalog_url,
            ],
            'recentEnquiries' => $recentEnquiries->map(fn ($enquiry) => [
                'id' => $enquiry->id,
                'customer_name' => $enquiry->customer_name,
                'customer_phone' => $enquiry->customer_phone,
                'status' => $enquiry->status,
                'created_at' => optional($enquiry->created_at)->format('d M Y'),
                'car' => $enquiry->car ? [
                    'title' => $enquiry->car->title,
                    'edit_url' => route('dealer.cars.edit', $enquiry->car),
                ] : null,
                'show_url' => route('dealer.enquiries.show', $enquiry),
            ]),
            'actions' => [
                'addCar' => route('dealer.cars.create'),
                'inventory' => route('dealer.cars.index'),
                'wallet' => route('dealer.wallet.index'),
                'plans' => route('dealer.plans.index'),
                'enquiries' => route('dealer.enquiries.index'),
                'profile' => route('dealer.profile.edit'),
            ],
        ]);
    }
}
