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
        RateLimiter::for('otp', function (Request $request) {
            $phone = preg_replace('/\D+/', '', (string) $request->input('phone', $request->input('mobile', '')));

            return [
                Limit::perMinute(5)->by('otp-ip:'.$request->ip()),
                Limit::perMinutes(60, 15)->by('otp-phone:'.($phone !== '' ? $phone : $request->ip())),
            ];
        });

        RateLimiter::for('auth', function (Request $request) {
            $identifier = strtolower((string) $request->input('email', $request->input('phone', '')));

            return [
                Limit::perMinute(10)->by('auth-ip:'.$request->ip()),
                Limit::perMinute(10)->by('auth-id:'.($identifier !== '' ? $identifier : $request->ip())),
            ];
        });
    }
}
