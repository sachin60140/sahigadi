<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth('admin')->user();

        if (! $user) {
            return redirect()->route('admin.login')->with('error', 'Please login to continue.');
        }

        if (! $user->isAdmin()) {
            abort(403, 'Access denied.');
        }

        return $next($request);
    }
}
