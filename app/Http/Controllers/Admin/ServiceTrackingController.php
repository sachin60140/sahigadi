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
            $item->type = 'dealer';
            $item->user_name = $item->dealer->name ?? 'N/A';

            return $item;
        })->concat($customerSearches->map(function ($item) {
            $item->type = 'customer';
            $item->user_name = $item->customer_name ?? 'N/A';

            return $item;
        }))->sortByDesc('created_at');

        $totalSearches = $allSearches->count();
        $successfulSearches = $allSearches->where('is_success', true)->count();
        $totalRevenue = $allSearches->where('is_success', true)->sum('charge_amount');
        $charge = Setting::getVehicleSearchCharge();
        $dealerCharge = Setting::getDealerVehicleSearchCharge();

        return view('admin.service-tracking.vehicle-search', compact('allSearches', 'totalSearches', 'successfulSearches', 'totalRevenue', 'charge', 'dealerCharge'));
    }

    public function serviceHistory(Request $request)
    {
        $query = AdminServiceHistory::with('dealer');
        $customerQuery = CustomerServiceHistory::select('*', 'vehicle_number as reg_number', 'paid_amount as charge_amount');

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
            $item->type = 'dealer';
            $item->user_name = $item->dealer->name ?? 'N/A';
            $item->vehicle_number = $item->vehicle_number;

            return $item;
        })->concat($customerSearches->map(function ($item) {
            $item->type = 'customer';
            $item->user_name = $item->customer_name ?? 'N/A';

            return $item;
        }))->sortByDesc('created_at');

        $totalSearches = $allSearches->count();
        $successfulSearches = $allSearches->where('is_success', true)->count();
        $totalRevenue = $allSearches->where('is_success', true)->sum('paid_amount');
        $charge = Setting::getServiceHistoryCharge();
        $dealerCharge = Setting::getDealerServiceHistoryCharge();

        return view('admin.service-tracking.service-history', compact('allSearches', 'totalSearches', 'successfulSearches', 'totalRevenue', 'charge', 'dealerCharge'));
    }

    public function challanSearch(Request $request)
    {
        $query = AdminChallanSearch::with('dealer');
        $customerQuery = CustomerChallanSearch::select('*', 'vehicle_number as reg_number', 'paid_amount as charge_amount');

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
            $item->type = 'dealer';
            $item->user_name = $item->dealer->name ?? 'N/A';

            return $item;
        })->concat($customerSearches->map(function ($item) {
            $item->type = 'customer';
            $item->user_name = $item->customer_name ?? 'N/A';

            return $item;
        }))->sortByDesc('created_at');

        $totalSearches = $allSearches->count();
        $successfulSearches = $allSearches->where('is_success', true)->count();
        $totalRevenue = $allSearches->where('is_success', true)->sum('paid_amount');
        $charge = Setting::getChallanCharge();
        $dealerCharge = Setting::getDealerChallanCharge();

        return view('admin.service-tracking.challan-search', compact('allSearches', 'totalSearches', 'successfulSearches', 'totalRevenue', 'charge', 'dealerCharge'));
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
}
