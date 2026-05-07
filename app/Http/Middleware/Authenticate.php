<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    protected function redirectTo(Request $request): ?string
    {
        if (! $request->expectsJson()) {
            if ($request->is('admin') || $request->is('admin/*')) {
                return route('admin.login');
            }
            if ($request->is('dealer') || $request->is('dealer/*')) {
                return route('dealer.login');
            }
            if ($request->is('customer') || $request->is('customer/*')) {
                return route('customer.login');
            }

            return route('customer.login'); // default fallback
        }

        return null;
    }
}
