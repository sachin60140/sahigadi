<?php

namespace App\Http\Middleware;

use App\Models\Dealer;
use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDealerApiAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Setting::isDealerApiEnabled()) {
            return response()->json([
                'success' => false,
                'message' => 'The vehicle search API is currently unavailable.',
            ], 503);
        }

        $dealer = $request->user();

        if (! $dealer instanceof Dealer) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid API credentials.',
            ], 401);
        }

        if ($dealer->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Your dealer account is not approved for API access.',
            ], 403);
        }

        if (! $dealer->api_enabled) {
            return response()->json([
                'success' => false,
                'message' => 'API access is not enabled for your account. Contact SAHI GADI support.',
            ], 403);
        }

        // A mobile app login token must never grant partner API access.
        $token = $dealer->currentAccessToken();

        if (! $token || ! $token->can(Dealer::PARTNER_API_ABILITY)) {
            return response()->json([
                'success' => false,
                'message' => 'This token is not valid for the partner API. Use a partner API key.',
            ], 403);
        }

        return $next($request);
    }
}
