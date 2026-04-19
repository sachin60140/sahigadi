<?php

namespace App\Http\Controllers\Admin;

use App\Exports\VehicleSearchesExport;
use App\Http\Controllers\Controller;
use App\Models\AdminVehicleSearch;
use App\Models\Dealer;
use App\Models\Setting;
use App\Models\VehicleDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class VehicleSearchController extends Controller
{
    public function index(Request $request)
    {
        $query = AdminVehicleSearch::with('dealer');

        if ($request->filled('search')) {
            $query->where('registration_number', 'like', '%'.strtoupper($request->search).'%');
        }

        if ($request->filled('dealer_id')) {
            $query->where('dealer_id', $request->dealer_id);
        }

        if ($request->filled('status')) {
            if ($request->status === 'success') {
                $query->where('is_success', true);
            } elseif ($request->status === 'failed') {
                $query->where('is_success', false);
            }
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $searches = $query->orderBy('created_at', 'desc')->paginate(25);
        $dealers = Dealer::orderBy('name')->get();
        $charge = Setting::getVehicleSearchCharge();
        $totalSearches = AdminVehicleSearch::count();
        $successfulSearches = AdminVehicleSearch::where('is_success', true)->count();
        $totalRevenue = $searches->sum('charge_amount');

        return view('admin.vehicle-searches.index', compact('searches', 'dealers', 'charge', 'totalSearches', 'successfulSearches', 'totalRevenue'));
    }

    public function show(AdminVehicleSearch $vehicleSearch)
    {
        $vehicleSearch->load('dealer');

        $dealerSearch = VehicleDetail::where('dealer_id', $vehicleSearch->dealer_id)
            ->where('registration_number', $vehicleSearch->registration_number)
            ->first();

        return view('admin.vehicle-searches.show', compact('vehicleSearch', 'dealerSearch'));
    }

    public function settings(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'charge' => 'required|numeric|min:0',
                'dealer_charge' => 'required|numeric|min:0',
            ]);

            Setting::setVehicleSearchCharge($request->charge);
            Setting::setDealerVehicleSearchCharge($request->dealer_charge);

            return redirect()->back()->with('success', 'Service charges updated successfully!');
        }

        $charge = Setting::getVehicleSearchCharge();
        $dealerCharge = Setting::getDealerVehicleSearchCharge();
        $totalSearches = AdminVehicleSearch::count();
        $successfulSearches = AdminVehicleSearch::where('is_success', true)->count();
        $totalRevenue = AdminVehicleSearch::where('is_success', true)->sum('charge_amount');

        return view('admin.vehicle-searches.settings', compact('charge', 'dealerCharge', 'totalSearches', 'successfulSearches', 'totalRevenue'));
    }

    public function exportExcel(Request $request)
    {
        $query = AdminVehicleSearch::with('dealer');

        if ($request->filled('search')) {
            $query->where('registration_number', 'like', '%'.strtoupper($request->search).'%');
        }

        if ($request->filled('dealer_id')) {
            $query->where('dealer_id', $request->dealer_id);
        }

        if ($request->filled('status')) {
            if ($request->status === 'success') {
                $query->where('is_success', true);
            } elseif ($request->status === 'failed') {
                $query->where('is_success', false);
            }
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $searches = $query->orderBy('created_at', 'desc')->get();

        return Excel::download(new VehicleSearchesExport($searches), 'vehicle-searches-'.date('Y-m-d').'.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $query = AdminVehicleSearch::with('dealer');

        if ($request->filled('search')) {
            $query->where('registration_number', 'like', '%'.strtoupper($request->search).'%');
        }

        if ($request->filled('dealer_id')) {
            $query->where('dealer_id', $request->dealer_id);
        }

        if ($request->filled('status')) {
            if ($request->status === 'success') {
                $query->where('is_success', true);
            } elseif ($request->status === 'failed') {
                $query->where('is_success', false);
            }
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $searches = $query->orderBy('created_at', 'desc')->get();
        $totalRevenue = $searches->where('is_success', true)->sum('charge_amount');

        $pdf = Pdf::loadView('admin.vehicle-searches.pdf', compact('searches', 'totalRevenue'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('vehicle-searches-'.date('Y-m-d').'.pdf');
    }

    public function downloadSinglePdf(AdminVehicleSearch $vehicleSearch)
    {
        $vehicleSearch->load('dealer');

        $dealerSearch = VehicleDetail::where('dealer_id', $vehicleSearch->dealer_id)
            ->where('registration_number', $vehicleSearch->registration_number)
            ->first();

        $pdf = Pdf::loadView('admin.vehicle-searches.single-pdf', compact('vehicleSearch', 'dealerSearch'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('rc-'.$vehicleSearch->registration_number.'.pdf');
    }
}
