<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ServiceHistoryExport;
use App\Http\Controllers\Controller;
use App\Models\AdminServiceHistory;
use App\Models\Dealer;
use App\Models\ServiceHistory;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ServiceHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = AdminServiceHistory::with('dealer');

        if ($request->filled('search')) {
            $query->where('vehicle_number', 'like', '%'.strtoupper($request->search).'%');
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
        $charge = Setting::getServiceHistoryCharge();
        $totalRevenue = $searches->sum('charge_amount');

        return view('admin.service-histories.index', compact('searches', 'dealers', 'charge', 'totalRevenue'));
    }

    public function show(AdminServiceHistory $serviceHistory)
    {
        $serviceHistory->load('dealer');

        $dealerSearch = ServiceHistory::where('dealer_id', $serviceHistory->dealer_id)
            ->where('vehicle_number', $serviceHistory->vehicle_number)
            ->with('records')
            ->first();

        return view('admin.service-histories.show', compact('serviceHistory', 'dealerSearch'));
    }

    public function settings(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'charge' => 'required|numeric|min:0',
                'dealer_charge' => 'required|numeric|min:0',
            ]);

            Setting::setServiceHistoryCharge($request->charge);
            Setting::setDealerServiceHistoryCharge($request->dealer_charge);

            return redirect()->back()->with('success', 'Service charges updated successfully!');
        }

        $charge = Setting::getServiceHistoryCharge();
        $dealerCharge = Setting::getDealerServiceHistoryCharge();
        $totalSearches = AdminServiceHistory::count();
        $successfulSearches = AdminServiceHistory::where('is_success', true)->count();
        $totalRevenue = AdminServiceHistory::where('is_success', true)->sum('charge_amount');

        return view('admin.service-histories.settings', compact('charge', 'dealerCharge', 'totalSearches', 'successfulSearches', 'totalRevenue'));
    }

    public function exportExcel(Request $request)
    {
        $query = AdminServiceHistory::with('dealer');

        if ($request->filled('search')) {
            $query->where('vehicle_number', 'like', '%'.strtoupper($request->search).'%');
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

        return Excel::download(new ServiceHistoryExport($searches), 'service-histories-'.date('Y-m-d').'.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $query = AdminServiceHistory::with('dealer');

        if ($request->filled('search')) {
            $query->where('vehicle_number', 'like', '%'.strtoupper($request->search).'%');
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

        $pdf = Pdf::loadView('admin.service-histories.pdf', compact('searches', 'totalRevenue'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('service-histories-'.date('Y-m-d').'.pdf');
    }

    public function downloadSinglePdf(AdminServiceHistory $serviceHistory)
    {
        $serviceHistory->load('dealer');

        $dealerSearch = ServiceHistory::where('dealer_id', $serviceHistory->dealer_id)
            ->where('vehicle_number', $serviceHistory->vehicle_number)
            ->with('records')
            ->first();

        $pdf = Pdf::loadView('admin.service-histories.single-pdf', compact('serviceHistory', 'dealerSearch'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('service-history-'.$serviceHistory->vehicle_number.'.pdf');
    }
}
