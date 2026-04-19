<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminChallanSearch;
use App\Models\Dealer;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ChallanSearchController extends Controller
{
    public function index(Request $request)
    {
        $query = AdminChallanSearch::with('dealer');

        if ($request->filled('search')) {
            $query->where('vehicle_number', 'like', '%'.strtoupper($request->search).'%');
        }

        if ($request->filled('dealer_id')) {
            $query->where('dealer_id', $request->dealer_id);
        }

        if ($request->filled('status')) {
            if ($request->status == 'success') {
                $query->where('is_success', true);
            } else {
                $query->where('is_success', false);
            }
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $searches = $query->orderBy('created_at', 'desc')->paginate(20);
        $dealers = Dealer::orderBy('name')->get();
        $charge = Setting::getChallanCharge();

        $totalRevenue = AdminChallanSearch::where('is_success', true)
            ->when($request->filled('from_date'), fn ($q) => $q->whereDate('created_at', '>=', $request->from_date))
            ->when($request->filled('to_date'), fn ($q) => $q->whereDate('created_at', '<=', $request->to_date))
            ->sum('charge_amount');

        return view('admin.challan-searches.index', compact('searches', 'dealers', 'charge', 'totalRevenue'));
    }

    public function show(AdminChallanSearch $challanSearch)
    {
        return view('admin.challan-searches.show', compact('challanSearch'));
    }

    public function downloadPdf(AdminChallanSearch $challanSearch)
    {
        $pdf = Pdf::loadView('dealer.challan-searches.pdf', compact('challanSearch'));

        return $pdf->download('e-challan-'.$challanSearch->vehicle_number.'.pdf');
    }

    public function settings(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'charge' => 'required|numeric|min:0',
                'dealer_charge' => 'required|numeric|min:0',
            ]);

            Setting::setChallanCharge($request->charge);
            Setting::setDealerChallanCharge($request->dealer_charge);

            return redirect()->back()->with('success', 'Service charges updated successfully!');
        }

        $charge = Setting::getChallanCharge();
        $dealerCharge = Setting::getDealerChallanCharge();
        $totalSearches = AdminChallanSearch::count();
        $successfulSearches = AdminChallanSearch::where('is_success', true)->count();
        $totalRevenue = AdminChallanSearch::where('is_success', true)->sum('charge_amount');

        return view('admin.challan-searches.settings', compact('charge', 'dealerCharge', 'totalSearches', 'successfulSearches', 'totalRevenue'));
    }

    public function exportExcel(Request $request)
    {
        $query = AdminChallanSearch::with('dealer');

        if ($request->filled('search')) {
            $query->where('vehicle_number', 'like', '%'.strtoupper($request->search).'%');
        }
        if ($request->filled('dealer_id')) {
            $query->where('dealer_id', $request->dealer_id);
        }
        if ($request->filled('status')) {
            $query->where('is_success', $request->status == 'success');
        }
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $searches = $query->orderBy('created_at', 'desc')->get();

        $filename = 'e-challan-searches-'.now()->format('Y-m-d').'.csv';
        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="'.$filename.'"'];

        $callback = function () use ($searches) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Dealer', 'Vehicle Number', 'Challans', 'Total Amount', 'Status', 'Date']);
            foreach ($searches as $s) {
                fputcsv($handle, [
                    $s->id,
                    $s->dealer->name ?? 'N/A',
                    $s->vehicle_number,
                    $s->challan_count,
                    $s->total_amount ?? 0,
                    $s->is_success ? 'Success' : 'Failed',
                    $s->created_at->format('d M Y'),
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $query = AdminChallanSearch::with('dealer');

        if ($request->filled('search')) {
            $query->where('vehicle_number', 'like', '%'.strtoupper($request->search).'%');
        }
        if ($request->filled('dealer_id')) {
            $query->where('dealer_id', $request->dealer_id);
        }
        if ($request->filled('status')) {
            $query->where('is_success', $request->status == 'success');
        }
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $searches = $query->orderBy('created_at', 'desc')->get();

        $totalRevenue = $searches->where('is_success', true)->sum('total_amount');

        $pdf = Pdf::loadView('admin.challan-searches.pdf', compact('searches', 'totalRevenue'));

        return $pdf->download('e-challan-searches-'.now()->format('Y-m-d').'.pdf');
    }
}
