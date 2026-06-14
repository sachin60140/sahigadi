<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerMarutiServiceHistory;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerMarutiServiceHistoryController extends Controller
{
    public function index(Request $request)
    {
        $filters = $this->filters($request);
        $searches = $this->applyFilters(CustomerMarutiServiceHistory::withCount('records'), $request)->latest()->paginate(20)->withQueryString();

        return Inertia::render('Admin/ProviderServiceHistories/Index', [
            'page' => ['title' => 'Maruti Customer Service History', 'heading' => 'Monitor customer-paid Maruti service lookups.', 'description' => 'Review customer identity, workshop record counts, payments and API outcomes.', 'channel' => 'customer', 'filterUrl' => route('admin.customer-maruti-service-histories.index')],
            'searches' => $searches->through(fn (CustomerMarutiServiceHistory $search) => $this->mapSearch($search)),
            'dealers' => [],
            'filters' => $filters,
            'stats' => ['total' => CustomerMarutiServiceHistory::count(), 'successful' => CustomerMarutiServiceHistory::where('is_success', true)->count(), 'failed' => CustomerMarutiServiceHistory::where('is_success', false)->count(), 'revenue' => (float) CustomerMarutiServiceHistory::where('is_success', true)->where('is_refunded', false)->sum('paid_amount'), 'charge' => (float) \App\Models\Setting::getMarutiServiceHistoryCharge()],
            'actions' => ['exportExcel' => route('admin.customer-maruti-service-histories.exportExcel', array_filter($filters)), 'exportPdf' => route('admin.customer-maruti-service-histories.exportPdf', array_filter($filters))],
        ]);
    }

    public function show(CustomerMarutiServiceHistory $marutiServiceHistory)
    {
        $marutiServiceHistory->load('records');
        return Inertia::render('Admin/ProviderServiceHistories/Show', [
            'page' => ['title' => 'Maruti Customer Service Details', 'provider' => 'Maruti', 'channel' => 'customer'],
            'search' => array_merge($this->mapSearch($marutiServiceHistory), [
                'customer_email' => $marutiServiceHistory->customer_email,
                'payment_id' => $marutiServiceHistory->razorpay_payment_id,
                'records' => $marutiServiceHistory->records->map(fn ($record) => $this->mapRecord($record))->values(),
                'actions' => ['back' => route('admin.customer-maruti-service-histories.index'), 'pdf' => route('admin.customer-maruti-service-histories.downloadPdf', $marutiServiceHistory)],
            ]),
        ]);
    }

    public function exportExcel(Request $request)
    {
        $searches = $this->applyFilters(CustomerMarutiServiceHistory::query(), $request)->latest()->get();

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
        $searches = $this->applyFilters(CustomerMarutiServiceHistory::query(), $request)->latest()->get();
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

    private function applyFilters($query, Request $request)
    {
        if ($request->filled('search')) {
            $term = (string) $request->search;
            $query->where(fn ($q) => $q->where('vehicle_number', 'like', '%'.strtoupper($term).'%')->orWhere('customer_name', 'like', '%'.$term.'%')->orWhere('customer_phone', 'like', '%'.$term.'%'));
        }
        if ($request->filled('status')) $query->where('is_success', $request->status === 'success');
        if ($request->filled('from_date')) $query->whereDate('created_at', '>=', $request->from_date);
        if ($request->filled('to_date')) $query->whereDate('created_at', '<=', $request->to_date);
        return $query;
    }

    private function filters(Request $request): array
    {
        return ['search' => (string) $request->query('search', ''), 'dealer_id' => '', 'status' => (string) $request->query('status', ''), 'from_date' => (string) $request->query('from_date', ''), 'to_date' => (string) $request->query('to_date', '')];
    }

    private function mapSearch(CustomerMarutiServiceHistory $search): array
    {
        return ['id' => $search->id, 'vehicle_number' => $search->vehicle_number, 'service_count' => (int) ($search->records_count ?? $search->records()->count()), 'amount' => (float) ($search->paid_amount ?? 0), 'is_success' => (bool) $search->is_success, 'error_message' => $search->error_message, 'created_at' => optional($search->created_at)->format('d M Y, h:i A'), 'subject_name' => $search->customer_name ?: 'Guest customer', 'subject_phone' => $search->customer_phone, 'actions' => ['show' => route('admin.customer-maruti-service-histories.show', $search), 'pdf' => route('admin.customer-maruti-service-histories.downloadPdf', $search)]];
    }

    private function mapRecord($record): array
    {
        return ['date' => optional($record->svc_date)->format('d M Y'), 'category' => $record->service_cate, 'work_type' => $record->work_type, 'dealer_name' => $record->dealer_name, 'location' => $record->dealer_address, 'job_card' => $record->register_no, 'ro_no' => $record->repair_order_no, 'bill_no' => $record->repair_order_bill_no, 'part_amount' => (float) ($record->part_amount ?? 0), 'labour_amount' => (float) ($record->labour_amount ?? 0), 'total_amount' => (float) ($record->total_amount ?? 0), 'mileage' => $record->mileage, 'status' => $record->status];
    }
}
