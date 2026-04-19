<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DealerApproval
{
    public function handle(Request $request, Closure $next): Response
    {
        $dealer = auth('dealer')->user();

        if (! $dealer) {
            return redirect()->route('dealer.login')->with('error', 'Please login to continue.');
        }

        if ($dealer->status === 'pending') {
            return redirect()->route('dealer.dashboard')->with('warning', 'Your account is pending approval.');
        }

        if ($dealer->status === 'rejected') {
            auth('dealer')->logout();

            return redirect()->route('dealer.login')->with('error', 'Your account has been rejected. Contact support.');
        }

        return $next($request);
    }
}
