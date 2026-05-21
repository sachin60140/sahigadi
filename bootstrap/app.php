<?php

use App\Http\Middleware\AdminAccess;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\DealerApproval;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'dealer.approval' => DealerApproval::class,
            'admin.access' => AdminAccess::class,
            'auth' => Authenticate::class,
            'customer.profile.complete' => \App\Http\Middleware\CheckCustomerProfileCompletion::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'dealer/payments/phonepe/callback',
            'dealer/payments/phonepe/webhook',
            'payments/phonepe/webhook',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
