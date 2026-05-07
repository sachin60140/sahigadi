<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerMahindraServiceHistory;
use App\Models\Setting;
use Illuminate\Http\Request;

class MahindraServiceHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = CustomerMahindraServiceHistory::query();

        if ($request->filled('search')) {
            $query->where('vehicle_number', 'like', '%'.strtoupper($request->search).'%')
                  ->orWhere('customer_name', 'like', '%'.$request->search.'%')
                  ->orWhere('customer_phone', 'like', '%'.$request->search.'%');
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
        $charge = Setting::getMahindraServiceHistoryCharge();

        $totalRevenue = CustomerMahindraServiceHistory::where('is_success', true)
            ->when($request->filled('from_date'), fn ($q) => $q->whereDate('created_at', '>=', $request->from_date))
            ->when($request->filled('to_date'), fn ($q) => $q->whereDate('created_at', '<=', $request->to_date))
            ->sum('paid_amount');

        return view('admin.mahindra-service-histories.index', compact('searches', 'charge', 'totalRevenue'));
    }

    public function show(CustomerMahindraServiceHistory $mahindraServiceHistory)
    {
        $mahindraServiceHistory->load('records');
        return view('admin.mahindra-service-histories.show', compact('mahindraServiceHistory'));
    }

    public function settings(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'charge' => 'required|numeric|min:0',
                'dealer_charge' => 'required|numeric|min:0',
            ]);

            Setting::setMahindraServiceHistoryCharge($request->charge);
            Setting::setDealerMahindraServiceHistoryCharge($request->dealer_charge);

            return redirect()->back()->with('success', 'Service charges updated successfully!');
        }

        $charge = Setting::getMahindraServiceHistoryCharge();
        $dealerCharge = Setting::getDealerMahindraServiceHistoryCharge();
        $totalSearches = CustomerMahindraServiceHistory::count();
        $successfulSearches = CustomerMahindraServiceHistory::where('is_success', true)->count();
        $totalRevenue = CustomerMahindraServiceHistory::where('is_success', true)->sum('paid_amount');

        return view('admin.mahindra-service-histories.settings', compact('charge', 'dealerCharge', 'totalSearches', 'successfulSearches', 'totalRevenue'));
    }

    public function exportExcel(Request $request)
    {
        $query = CustomerMahindraServiceHistory::query();

        if ($request->filled('search')) {
            $query->where('vehicle_number', 'like', '%'.strtoupper($request->search).'%')
                  ->orWhere('customer_name', 'like', '%'.$request->search.'%')
                  ->orWhere('customer_phone', 'like', '%'.$request->search.'%');
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

        $filename = 'mahindra-customer-service-histories-'.now()->format('Y-m-d').'.csv';
        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="'.$filename.'"'];

        $callback = function () use ($searches) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Customer Name', 'Phone', 'Vehicle Number', 'Amount Paid', 'Status', 'Date']);
            foreach ($searches as $s) {
                fputcsv($handle, [
                    $s->id,
                    $s->customer_name ?? 'N/A',
                    $s->customer_phone ?? 'N/A',
                    $s->vehicle_number,
                    $s->paid_amount ?? 0,
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
        $query = CustomerMahindraServiceHistory::query();

        if ($request->filled('search')) {
            $query->where('vehicle_number', 'like', '%'.strtoupper($request->search).'%');
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
        $totalRevenue = $searches->where('is_success', true)->sum('paid_amount');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.mahindra-service-histories.pdf', compact('searches', 'totalRevenue'));

        return $pdf->download('mahindra-customer-service-histories-'.now()->format('Y-m-d').'.pdf');
    }

    public function downloadPdf(CustomerMahindraServiceHistory $mahindraServiceHistory)
    {
        $mahindraServiceHistory->load('records');
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.mahindra-service-histories.single-pdf', compact('mahindraServiceHistory'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('mahindra-service-history-'.$mahindraServiceHistory->vehicle_number.'.pdf');
    }
}
