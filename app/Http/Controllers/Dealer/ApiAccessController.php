<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Models\AdminVehicleSearch;
use App\Models\Dealer;
use App\Models\Setting;
use Inertia\Inertia;

class ApiAccessController extends Controller
{
    public function index()
    {
        $dealer = auth('dealer')->user();
        $key = $dealer->partnerApiTokens()->latest()->first();

        $calls = AdminVehicleSearch::where('dealer_id', $dealer->id)->where('channel', 'api');

        return Inertia::render('Dealer/Api/Index', [
            'api' => [
                'globally_enabled' => Setting::isDealerApiEnabled(),
                'enabled_for_dealer' => (bool) $dealer->api_enabled,
                'charge' => (float) Setting::getDealerApiVehicleSearchCharge(),
                'wallet_balance' => (float) $dealer->walletBalance(),
                'base_url' => url('/api/v1'),
                'has_key' => (bool) $key,
                'key_created_at' => optional($key?->created_at)->format('d M Y, h:i A'),
                'key_last_used_at' => optional($key?->last_used_at)->format('d M Y, h:i A'),
            ],
            'usage' => [
                'total' => (clone $calls)->count(),
                'successful' => (clone $calls)->where('is_success', true)->count(),
                'spent' => (float) (clone $calls)->where('is_success', true)->sum('charge_amount'),
                'recent' => (clone $calls)->latest()->limit(10)->get()->map(fn (AdminVehicleSearch $call) => [
                    'id' => $call->id,
                    'registration_number' => $call->registration_number,
                    'is_success' => (bool) $call->is_success,
                    'charge' => (float) $call->charge_amount,
                    'created_at' => optional($call->created_at)->format('d M Y, h:i A'),
                ])->values(),
            ],
            'actions' => [
                'generate' => route('dealer.api-access.generate'),
                'revoke' => route('dealer.api-access.revoke'),
                'wallet' => route('dealer.wallet.index'),
            ],
        ]);
    }

    public function generate()
    {
        $dealer = auth('dealer')->user();

        if (! Setting::isDealerApiEnabled() || ! $dealer->api_enabled) {
            return back()->with('error', 'API access is not available for your account right now.');
        }

        // A dealer holds one active key; generating a new one replaces it.
        $dealer->partnerApiTokens()->delete();

        $plainTextToken = $dealer->createToken(
            Dealer::PARTNER_API_TOKEN,
            [Dealer::PARTNER_API_ABILITY]
        )->plainTextToken;

        return back()
            ->with('success', 'API key generated. Copy it now - it will not be shown again.')
            ->with('api_key', $plainTextToken);
    }

    public function revoke()
    {
        auth('dealer')->user()->partnerApiTokens()->delete();

        return back()->with('success', 'Your API key has been revoked.');
    }
}
