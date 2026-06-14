<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerMahindraServiceHistory;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MahindraServiceHistoryController extends Controller
{
    public function index(Request $request)
    {
        $filters = $this->filters($request);
        $searches = $this->applyFilters(CustomerMahindraServiceHistory::withCount('records'), $request)->latest()->paginate(20)->withQueryString();

        return Inertia::render('Admin/ProviderServiceHistories/Index', [
            'page' => ['title' => 'Mahindra Customer Service History', 'heading' => 'Monitor customer-paid Mahindra service lookups.', 'description' => 'Review customer identity, workshop record counts, payments and API outcomes.', 'channel' => 'customer', 'filterUrl' => route('admin.mahindra-service-histories.index')],
            'searches' => $searches->through(fn (CustomerMahindraServiceHistory $search) => $this->mapSearch($search)),
            'dealers' => [], 'filters' => $filters,
            'stats' => ['total' => CustomerMahindraServiceHistory::count(), 'successful' => CustomerMahindraServiceHistory::where('is_success', true)->count(), 'failed' => CustomerMahindraServiceHistory::where('is_success', false)->count(), 'revenue' => (float) CustomerMahindraServiceHistory::where('is_success', true)->sum('paid_amount'), 'charge' => (float) Setting::getMahindraServiceHistoryCharge()],
            'actions' => ['exportExcel' => route('admin.mahindra-service-histories.exportExcel', array_filter($filters)), 'exportPdf' => route('admin.mahindra-service-histories.exportPdf', array_filter($filters))],
        ]);
    }

    public function show(CustomerMahindraServiceHistory $mahindraServiceHistory)
    {
        $mahindraServiceHistory->load('records');
        return Inertia::render('Admin/ProviderServiceHistories/Show', [
            'page' => ['title' => 'Mahindra Customer Service Details', 'provider' => 'Mahindra', 'channel' => 'customer'],
            'search' => array_merge($this->mapSearch($mahindraServiceHistory), [
                'customer_email' => $mahindraServiceHistory->customer_email, 'payment_id' => $mahindraServiceHistory->razorpay_payment_id,
                'records' => $mahindraServiceHistory->records->map(fn ($record) => $this->mapRecord($record))->values(),
                'actions' => ['back' => route('admin.mahindra-service-histories.index'), 'pdf' => route('admin.mahindra-service-histories.downloadPdf', $mahindraServiceHistory)],
            ]),
        ]);
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

        return Inertia::render('Admin/ServiceHistories/Settings', [
            'serviceName' => 'Mahindra Service History', 'charges' => ['customer' => (float) $charge, 'dealer' => (float) $dealerCharge],
            'stats' => ['total' => $totalSearches, 'successful' => $successfulSearches, 'revenue' => (float) $totalRevenue],
            'actions' => ['update' => route('admin.mahindra-service-histories.settings')],
        ]);
    }

    public function exportExcel(Request $request)
    {
        $searches = $this->applyFilters(CustomerMahindraServiceHistory::query(), $request)->latest()->get();

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
        $searches = $this->applyFilters(CustomerMahindraServiceHistory::query(), $request)->latest()->get();
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

    private function mapSearch(CustomerMahindraServiceHistory $search): array
    {
        return ['id' => $search->id, 'vehicle_number' => $search->vehicle_number, 'service_count' => (int) ($search->records_count ?? $search->records()->count()), 'amount' => (float) ($search->paid_amount ?? 0), 'is_success' => (bool) $search->is_success, 'error_message' => $search->error_message, 'created_at' => optional($search->created_at)->format('d M Y, h:i A'), 'subject_name' => $search->customer_name ?: 'Guest customer', 'subject_phone' => $search->customer_phone, 'actions' => ['show' => route('admin.mahindra-service-histories.show', $search), 'pdf' => route('admin.mahindra-service-histories.downloadPdf', $search)]];
    }

    private function mapRecord($record): array
    {
        return ['date' => optional($record->svc_date)->format('d M Y'), 'category' => $record->service_cate, 'work_type' => $record->work_type, 'dealer_name' => $record->dealer_name, 'location' => $record->dealer_address, 'job_card' => $record->register_no, 'ro_no' => $record->repair_order_no, 'bill_no' => $record->repair_order_bill_no, 'bill_date' => optional($record->repair_order_bill_date)->format('d M Y'), 'assistant' => $record->service_assistant_name, 'part_amount' => (float) ($record->part_amount ?? 0), 'labour_amount' => (float) ($record->labour_amount ?? 0), 'total_amount' => (float) ($record->total_amount ?? 0), 'mileage' => $record->mileage, 'status' => $record->status];
    }
}
