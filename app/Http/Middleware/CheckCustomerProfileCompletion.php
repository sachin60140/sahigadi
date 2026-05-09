<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCustomerProfileCompletion
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth('customer')->check()) {
            $customer = auth('customer')->user();
            
            // Re-calculate to be safe if not set properly (optional, maybe too heavy for every request, but good for first time)
            if ($customer->profile_completion_percentage === 0) {
                $customer->calculateProfileCompletion();
            }

            if ($customer->profile_completion_percentage < 75) {
                // Allow them to visit profile pages and logout
                $allowedRoutes = [
                    'customer.profile.edit',
                    'customer.profile.update',
                    'customer.logout',
                ];

                if (!in_array($request->route()->getName(), $allowedRoutes)) {
                    return redirect()->route('customer.profile.edit')
                        ->with('error', 'Please complete minimum 75% of your profile to continue.');
                }
            }
        }

        return $next($request);
    }
}
