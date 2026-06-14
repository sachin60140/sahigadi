<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'inertia';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'auth' => [
                'admin' => fn () => $request->user('admin') ? [
                    'id' => $request->user('admin')->id,
                    'name' => $request->user('admin')->name,
                    'email' => $request->user('admin')->email,
                ] : null,
                'dealer' => fn () => $request->user('dealer') ? [
                    'id' => $request->user('dealer')->id,
                    'name' => $request->user('dealer')->name,
                    'email' => $request->user('dealer')->email,
                    'phone' => $request->user('dealer')->phone,
                    'company_name' => $request->user('dealer')->company_name,
                    'dealer_unique_id' => $request->user('dealer')->dealer_unique_id,
                    'status' => $request->user('dealer')->status,
                    'slug' => $request->user('dealer')->slug,
                    'new_enquiries' => $request->user('dealer')->enquiries()->where('status', 'new')->count(),
                ] : null,
                'customer' => fn () => $request->user('customer') ? [
                    'id' => $request->user('customer')->id,
                    'name' => $request->user('customer')->name,
                    'email' => $request->user('customer')->email,
                    'phone' => $request->user('customer')->phone,
                    'customer_unique_id' => $request->user('customer')->customer_unique_id,
                    'profile_image_url' => $request->user('customer')->profile_image
                        ? asset('storage/'.$request->user('customer')->profile_image)
                        : null,
                    'profile_completion_percentage' => (int) $request->user('customer')->profile_completion_percentage,
                    'wallet_balance' => (float) ($request->user('customer')->wallet?->balance ?? 0),
                    'listing_count' => $request->user('customer')->listings()->count(),
                ] : null,
            ],
        ];
    }
}
