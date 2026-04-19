<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminMarutiServiceHistory;
use App\Models\Dealer;
use App\Models\MarutiServiceHistory;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class MarutiServiceHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = AdminMarutiServiceHistory::with('dealer');

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

        $searches = $query->orderBy('created_at', 'desc')->paginate(20);
        $dealers = Dealer::orderBy('name')->get();
        $charge = Setting::getDealerServiceHistoryCharge();

        $totalRevenue = AdminMarutiServiceHistory::where('is_success', true)
            ->when($request->filled('from_date'), fn ($q) => $q->whereDate('created_at', '>=', $request->from_date))
            ->when($request->filled('to_date'), fn ($q) => $q->whereDate('created_at', '<=', $request->to_date))
            ->sum('charge_amount');

        return view('admin.maruti-service-histories.index', compact('searches', 'dealers', 'charge', 'totalRevenue'));
    }

    public function show(AdminMarutiServiceHistory $marutiServiceHistory)
    {
        return view('admin.maruti-service-histories.show', compact('marutiServiceHistory'));
    }

    public function settings(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'charge' => 'required|numeric|min:0',
                'dealer_charge' => 'required|numeric|min:0',
            ]);

            Setting::setMarutiServiceHistoryCharge($request->charge);
            Setting::setDealerMarutiServiceHistoryCharge($request->dealer_charge);

            return redirect()->back()->with('success', 'Service charges updated successfully!');
        }

        $charge = Setting::getMarutiServiceHistoryCharge();
        $dealerCharge = Setting::getDealerMarutiServiceHistoryCharge();
        $totalSearches = AdminMarutiServiceHistory::count();
        $successfulSearches = AdminMarutiServiceHistory::where('is_success', true)->count();
        $totalRevenue = AdminMarutiServiceHistory::where('is_success', true)->sum('charge_amount');

        return view('admin.maruti-service-histories.settings', compact('charge', 'dealerCharge', 'totalSearches', 'successfulSearches', 'totalRevenue'));
    }

    public function exportExcel(Request $request)
    {
        $query = AdminMarutiServiceHistory::with('dealer');

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

        $filename = 'maruti-service-histories-'.now()->format('Y-m-d').'.csv';
        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="'.$filename.'"'];

        $callback = function () use ($searches) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Dealer', 'Vehicle Number', 'Services', 'Charge Amount', 'Status', 'Date']);
            foreach ($searches as $s) {
                fputcsv($handle, [
                    $s->id,
                    $s->dealer->name ?? 'N/A',
                    $s->vehicle_number,
                    $s->service_count,
                    $s->charge_amount ?? 0,
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
        $query = AdminMarutiServiceHistory::with('dealer');

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

        $totalRevenue = $searches->where('is_success', true)->sum('charge_amount');

        $pdf = Pdf::loadView('admin.maruti-service-histories.pdf', compact('searches', 'totalRevenue'));

        return $pdf->download('maruti-service-histories-'.now()->format('Y-m-d').'.pdf');
    }

    public function downloadPdf(AdminMarutiServiceHistory $marutiServiceHistory)
    {
        $dealerSearch = MarutiServiceHistory::where('dealer_id', $marutiServiceHistory->dealer_id)
            ->where('vehicle_number', $marutiServiceHistory->vehicle_number)
            ->with('records')
            ->first();

        $pdf = Pdf::loadView('admin.maruti-service-histories.single-pdf', compact('marutiServiceHistory', 'dealerSearch'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('maruti-service-history-'.$marutiServiceHistory->vehicle_number.'.pdf');
    }
}
