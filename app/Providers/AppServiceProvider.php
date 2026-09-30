<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        \Illuminate\Pagination\Paginator::useBootstrapFive();

        if (config('app.env') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        \App\Models\Customer::observe(\App\Observers\CustomerObserver::class);
        \App\Models\Dealer::observe(\App\Observers\DealerObserver::class);

        $this->configureRateLimiters();
    }

    /**
     * Throttle limiters for OTP delivery and authentication endpoints
     * (prevents SMS-bombing and OTP/password brute force).
     */
    protected function configureRateLimiters(): void
    {
        // The per-phone limit is the real abuse control. The per-IP limit is a
        // coarse backstop only: mobile carriers put thousands of subscribers
        // behind one NAT address, so a tight per-IP cap blocks unrelated
        // people who happen to share a carrier.
        RateLimiter::for('otp', function (Request $request) {
            $phone = preg_replace('/\D+/', '', (string) $request->input('phone', $request->input('mobile', '')));

            return [
                Limit::perMinute(60)->by('otp-ip:'.$request->ip()),
                Limit::perMinutes(60, 15)->by('otp-phone:'.($phone !== '' ? $phone : $request->ip())),
            ];
        });

        // Same reasoning as 'otp': the per-identifier limit stops credential
        // stuffing against one account; the per-IP limit stays loose enough
        // not to punish a whole carrier NAT.
        RateLimiter::for('auth', function (Request $request) {
            $identifier = strtolower((string) $request->input('email', $request->input('phone', '')));

            return [
                Limit::perMinute(60)->by('auth-ip:'.$request->ip()),
                Limit::perMinute(10)->by('auth-id:'.($identifier !== '' ? $identifier : $request->ip())),
            ];
        });

        // The public catalogue is browsed by every app user, and Indian mobile
        // carriers put thousands of subscribers behind one NAT address, so this
        // stays deliberately loose: it is a backstop against someone hammering
        // the endpoints, not the control that protects seller data. That is the
        // $hidden list on Car and CustomerCarListing - a rate limit cannot stop a
        // scrape of data the response should not contain in the first place.
        RateLimiter::for('public-catalogue', function (Request $request) {
            return Limit::perMinute(120)->by('catalogue-ip:'.$request->ip());
        });

        RateLimiter::for('api', function (Request $request) {
            $identifier = $request->user()?->getAuthIdentifier();

            return Limit::perMinute(60)->by($identifier ? 'dealer-api:'.$identifier : 'api-ip:'.$request->ip());
        });
    }
}
