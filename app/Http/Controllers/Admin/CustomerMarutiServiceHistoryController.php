<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerMarutiServiceHistory;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class CustomerMarutiServiceHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = CustomerMarutiServiceHistory::query();

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

        $totalRevenue = CustomerMarutiServiceHistory::where('is_success', true)
            ->when($request->filled('from_date'), fn ($q) => $q->whereDate('created_at', '>=', $request->from_date))
            ->when($request->filled('to_date'), fn ($q) => $q->whereDate('created_at', '<=', $request->to_date))
            ->sum('paid_amount');

        return view('admin.customer-maruti-service-histories.index', compact('searches', 'totalRevenue'));
    }

    public function show(CustomerMarutiServiceHistory $marutiServiceHistory)
    {
        $marutiServiceHistory->load('records');
        return view('admin.customer-maruti-service-histories.show', compact('marutiServiceHistory'));
    }

    public function exportExcel(Request $request)
    {
        $query = CustomerMarutiServiceHistory::query();

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

        $filename = 'maruti-customer-service-histories-'.now()->format('Y-m-d').'.csv';
        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="'.$filename.'"'];

        $callback = function () use ($searches) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Customer Name', 'Phone', 'Vehicle Number', 'Services Found', 'Charge Paid', 'Status', 'Date']);
            foreach ($searches as $search) {
                fputcsv($handle, [
                    $search->id,
                    $search->customer_name ?? 'N/A',
                    $search->customer_phone ?? 'N/A',
                    $search->vehicle_number,
                    $search->records ? $search->records->count() : 0,
                    $search->paid_amount,
                    $search->is_success ? 'Success' : 'Failed',
                    $search->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $query = CustomerMarutiServiceHistory::query();

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
        $totalRevenue = $searches->where('is_success', true)->sum('paid_amount');

        $pdf = Pdf::loadView('admin.customer-maruti-service-histories.pdf', compact('searches', 'totalRevenue'));

        return $pdf->download('maruti-customer-service-histories-'.now()->format('Y-m-d').'.pdf');
    }

    public function downloadPdf(CustomerMarutiServiceHistory $marutiServiceHistory)
    {
        $marutiServiceHistory->load('records');
        $pdf = Pdf::loadView('admin.customer-maruti-service-histories.single-pdf', compact('marutiServiceHistory'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('maruti-customer-service-history-'.$marutiServiceHistory->vehicle_number.'.pdf');
    }
}
