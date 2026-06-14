<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminChallanSearch;
use App\Models\AdminServiceHistory;
use App\Models\AdminVehicleSearch;
use App\Models\CustomerChallanSearch;
use App\Models\CustomerServiceHistory;
use App\Models\CustomerVehicleSearch;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;

class ServiceTrackingController extends Controller
{
    public function vehicleSearch(Request $request)
    {
        $query = AdminVehicleSearch::with('dealer');
        $customerQuery = CustomerVehicleSearch::select('*', 'registration_number as vehicle_number', 'paid_amount as charge_amount');

        if ($request->filled('search')) {
            $query->where('registration_number', 'like', '%'.strtoupper($request->search).'%');
            $customerQuery->where('registration_number', 'like', '%'.strtoupper($request->search).'%');
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
            $customerQuery->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
            $customerQuery->whereDate('created_at', '<=', $request->to_date);
        }

        $dealerSearches = $query->orderBy('created_at', 'desc')->get();
        $customerSearches = $customerQuery->orderBy('created_at', 'desc')->get();

        $allSearches = $dealerSearches->map(function ($item) {
            return [
                'id' => $item->id,
                'type' => 'dealer',
                'user_name' => $item->dealer?->company_name ?: ($item->dealer?->name ?? 'N/A'),
                'phone' => $item->dealer?->phone,
                'vehicle_number' => $item->registration_number,
                'is_success' => (bool) $item->is_success,
                'charge_amount' => (float) ($item->charge_amount ?? 0),
                'created_at_raw' => $item->created_at,
                'created_at' => $this->formatDateTime($item->created_at),
                'show_url' => route('admin.vehicle-searches.show', $item),
            ];
        })->concat($customerSearches->map(function ($item) {
            return [
                'id' => $item->id,
                'type' => 'customer',
                'user_name' => $item->customer_name ?? 'N/A',
                'phone' => $item->customer_phone,
                'vehicle_number' => $item->registration_number,
                'is_success' => (bool) $item->is_success,
                'charge_amount' => (float) ($item->paid_amount ?? 0),
                'created_at_raw' => $item->created_at,
                'created_at' => $this->formatDateTime($item->created_at),
                'show_url' => route('admin.customer-vehicle-searches.show', $item),
            ];
        }))->sortByDesc('created_at_raw')->map(function (array $item) {
            unset($item['created_at_raw']);

            return $item;
        })->values();

        $totalSearches = $allSearches->count();
        $successfulSearches = $allSearches->where('is_success', true)->count();
        $totalRevenue = $allSearches->where('is_success', true)->sum('charge_amount');
        $charge = Setting::getVehicleSearchCharge();
        $dealerCharge = Setting::getDealerVehicleSearchCharge();
        $page = max((int) $request->query('page', 1), 1);
        $paginatedSearches = new LengthAwarePaginator(
            $allSearches->forPage($page, 25)->values(),
            $allSearches->count(),
            25,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return Inertia::render('Admin/ServiceTracking/VehicleSearch', [
            'searches' => $paginatedSearches,
            'filters' => [
                'search' => (string) $request->query('search', ''),
                'from_date' => (string) $request->query('from_date', ''),
                'to_date' => (string) $request->query('to_date', ''),
            ],
            'stats' => [
                'total' => $totalSearches,
                'successful' => $successfulSearches,
                'failed' => $totalSearches - $successfulSearches,
                'revenue' => (float) $totalRevenue,
                'customer_charge' => (float) $charge,
                'dealer_charge' => (float) $dealerCharge,
            ],
            'actions' => [
                'dealerSearches' => route('admin.vehicle-searches.index'),
                'customerSearches' => route('admin.customer-vehicle-searches.index'),
                'settings' => route('admin.vehicle-searches.settings'),
            ],
        ]);
    }

    public function serviceHistory(Request $request)
    {
        $query = AdminServiceHistory::with('dealer');
        $customerQuery = CustomerServiceHistory::query();

        if ($request->filled('search')) {
            $query->where('vehicle_number', 'like', '%'.strtoupper($request->search).'%');
            $customerQuery->where('vehicle_number', 'like', '%'.strtoupper($request->search).'%');
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
            $customerQuery->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
            $customerQuery->whereDate('created_at', '<=', $request->to_date);
        }

        $dealerSearches = $query->orderBy('created_at', 'desc')->get();
        $customerSearches = $customerQuery->orderBy('created_at', 'desc')->get();

        $allSearches = $dealerSearches->map(function ($item) {
            return [
                'id' => $item->id,
                'type' => 'dealer',
                'user_name' => $item->dealer?->company_name ?: ($item->dealer?->name ?? 'N/A'),
                'phone' => $item->dealer?->phone,
                'vehicle_number' => $item->vehicle_number,
                'is_success' => (bool) $item->is_success,
                'service_count' => (int) ($item->service_count ?? 0),
                'charge_amount' => (float) ($item->charge_amount ?? 0),
                'created_at_raw' => $item->created_at,
                'created_at' => $this->formatDateTime($item->created_at),
                'show_url' => route('admin.service-histories.show', $item),
            ];
        })->concat($customerSearches->map(function ($item) {
            return [
                'id' => $item->id,
                'type' => 'customer',
                'user_name' => $item->customer_name ?? 'N/A',
                'phone' => $item->customer_phone,
                'vehicle_number' => $item->vehicle_number,
                'is_success' => (bool) $item->is_success,
                'service_count' => $item->records()->count(),
                'charge_amount' => (float) ($item->paid_amount ?? 0),
                'created_at_raw' => $item->created_at,
                'created_at' => $this->formatDateTime($item->created_at),
                'show_url' => route('admin.customer-transactions.show', ['id' => $item->id, 'type' => 'mahindra']),
            ];
        }))->sortByDesc('created_at_raw')->map(function (array $item) {
            unset($item['created_at_raw']);

            return $item;
        })->values();

        $totalSearches = $allSearches->count();
        $successfulSearches = $allSearches->where('is_success', true)->count();
        $totalRevenue = $allSearches->where('is_success', true)->sum('charge_amount');
        $charge = Setting::getServiceHistoryCharge();
        $dealerCharge = Setting::getDealerServiceHistoryCharge();
        $paginatedSearches = $this->paginateCollection($allSearches, $request);

        return Inertia::render('Admin/ServiceTracking/ServiceHistory', [
            'searches' => $paginatedSearches,
            'filters' => [
                'search' => (string) $request->query('search', ''),
                'from_date' => (string) $request->query('from_date', ''),
                'to_date' => (string) $request->query('to_date', ''),
            ],
            'stats' => [
                'total' => $totalSearches,
                'successful' => $successfulSearches,
                'failed' => $totalSearches - $successfulSearches,
                'revenue' => (float) $totalRevenue,
                'customer_charge' => (float) $charge,
                'dealer_charge' => (float) $dealerCharge,
            ],
        ]);
    }

    public function challanSearch(Request $request)
    {
        $query = AdminChallanSearch::with('dealer');
        $customerQuery = CustomerChallanSearch::query();

        if ($request->filled('search')) {
            $query->where('vehicle_number', 'like', '%'.strtoupper($request->search).'%');
            $customerQuery->where('vehicle_number', 'like', '%'.strtoupper($request->search).'%');
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
            $customerQuery->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
            $customerQuery->whereDate('created_at', '<=', $request->to_date);
        }

        $dealerSearches = $query->orderBy('created_at', 'desc')->get();
        $customerSearches = $customerQuery->orderBy('created_at', 'desc')->get();

        $allSearches = $dealerSearches->map(function ($item) {
            return [
                'id' => $item->id,
                'type' => 'dealer',
                'user_name' => $item->dealer?->company_name ?: ($item->dealer?->name ?? 'N/A'),
                'phone' => $item->dealer?->phone,
                'vehicle_number' => $item->vehicle_number,
                'is_success' => (bool) $item->is_success,
                'challan_count' => (int) ($item->challan_count ?? 0),
                'fine_amount' => (float) ($item->total_amount ?? 0),
                'charge_amount' => (float) ($item->charge_amount ?? 0),
                'created_at_raw' => $item->created_at,
                'created_at' => $this->formatDateTime($item->created_at),
                'show_url' => route('admin.challan-searches.show', $item),
                'pdf_url' => route('admin.challan-searches.download-pdf', $item),
            ];
        })->concat($customerSearches->map(function ($item) {
            return [
                'id' => $item->id,
                'type' => 'customer',
                'user_name' => $item->customer_name ?? 'N/A',
                'phone' => $item->customer_phone,
                'vehicle_number' => $item->vehicle_number,
                'is_success' => (bool) $item->is_success,
                'challan_count' => (int) ($item->challan_count ?? 0),
                'fine_amount' => (float) ($item->total_amount ?? 0),
                'charge_amount' => (float) ($item->paid_amount ?? 0),
                'created_at_raw' => $item->created_at,
                'created_at' => $this->formatDateTime($item->created_at),
                'show_url' => route('admin.customer-transactions.show', ['id' => $item->id, 'type' => 'challan']),
                'pdf_url' => route('admin.service-tracking.challan-search.pdf', ['id' => $item->id, 'type' => 'customer']),
            ];
        }))->sortByDesc('created_at_raw')->map(function (array $item) {
            unset($item['created_at_raw']);

            return $item;
        })->values();

        $totalSearches = $allSearches->count();
        $successfulSearches = $allSearches->where('is_success', true)->count();
        $totalRevenue = $allSearches->where('is_success', true)->sum('charge_amount');
        $charge = Setting::getChallanCharge();
        $dealerCharge = Setting::getDealerChallanCharge();
        $paginatedSearches = $this->paginateCollection($allSearches, $request);

        return Inertia::render('Admin/ServiceTracking/ChallanSearch', [
            'searches' => $paginatedSearches,
            'filters' => [
                'search' => (string) $request->query('search', ''),
                'from_date' => (string) $request->query('from_date', ''),
                'to_date' => (string) $request->query('to_date', ''),
            ],
            'stats' => [
                'total' => $totalSearches,
                'successful' => $successfulSearches,
                'failed' => $totalSearches - $successfulSearches,
                'revenue' => (float) $totalRevenue,
                'fine_amount' => (float) $allSearches->where('is_success', true)->sum('fine_amount'),
                'customer_charge' => (float) $charge,
                'dealer_charge' => (float) $dealerCharge,
            ],
        ]);
    }

    public function showChallan(Request $request, $id)
    {
        $type = $request->query('type');
        if ($type === 'dealer') {
            return redirect()->route('admin.challan-searches.show', $id);
        } else {
            return redirect()->route('admin.customer-transactions.show', ['id' => $id, 'type' => 'challan']);
        }
    }

    public function downloadChallanPdf(Request $request, $id)
    {
        $type = $request->query('type');
        if ($type === 'dealer') {
            $challanSearch = AdminChallanSearch::findOrFail($id);
        } else {
            $challanSearch = CustomerChallanSearch::findOrFail($id);
            // Map customer columns to dealer view structure
            $challanSearch->charge_amount = $challanSearch->paid_amount;
        }

        $pdf = Pdf::loadView('dealer.challan-searches.pdf', compact('challanSearch'));
        return $pdf->download('e-challan-'.$challanSearch->vehicle_number.'.pdf');
    }

    private function formatDateTime($value): ?string
    {
        if (! $value) {
            return null;
        }

        try {
            return \Carbon\Carbon::parse($value)->format('d M Y, h:i A');
        } catch (\Throwable) {
            return (string) $value;
        }
    }

    private function paginateCollection($items, Request $request): LengthAwarePaginator
    {
        $page = max((int) $request->query('page', 1), 1);

        return new LengthAwarePaginator(
            $items->forPage($page, 25)->values(),
            $items->count(),
            25,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );
    }
}
