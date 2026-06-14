<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerVehicleSearch;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CustomerVehicleSearchController extends Controller
{
    public function index(Request $request)
    {
        $filters = $this->filters($request);
        $searches = $this->applyFilters(CustomerVehicleSearch::query(), $request)
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return Inertia::render('Admin/CustomerVehicleSearches/Index', [
            'searches' => $searches->through(fn (CustomerVehicleSearch $search) => $this->mapSearch($search)),
            'filters' => $filters,
            'stats' => [
                'total' => CustomerVehicleSearch::count(),
                'successful' => CustomerVehicleSearch::where('is_success', true)->count(),
                'failed' => CustomerVehicleSearch::where('is_success', false)->count(),
                'refunded' => CustomerVehicleSearch::where('is_refunded', true)->count(),
                'revenue' => (float) CustomerVehicleSearch::where('is_success', true)
                    ->where('is_refunded', false)
                    ->sum('paid_amount'),
            ],
            'actions' => [
                'dealerSearches' => route('admin.vehicle-searches.index'),
                'combinedLedger' => route('admin.service-tracking.vehicle-search'),
                'settings' => route('admin.vehicle-searches.settings'),
                'exportExcel' => route('admin.customer-vehicle-searches.exportExcel', array_filter($filters)),
                'exportPdf' => route('admin.customer-vehicle-searches.exportPdf', array_filter($filters)),
            ],
        ]);
    }

    public function show(CustomerVehicleSearch $vehicleSearch)
    {
        return Inertia::render('Admin/CustomerVehicleSearches/Show', [
            'search' => array_merge($this->mapSearch($vehicleSearch), [
                'customer_email' => $vehicleSearch->customer_email,
                'razorpay_order_id' => $vehicleSearch->razorpay_order_id,
                'razorpay_payment_id' => $vehicleSearch->razorpay_payment_id,
                'razorpay_refund_id' => $vehicleSearch->razorpay_refund_id,
                'vehicle_data' => $vehicleSearch->vehicle_data ?? [],
                'actions' => [
                    'back' => route('admin.customer-vehicle-searches.index'),
                    'pdf' => route('admin.customer-vehicle-searches.downloadPdf', $vehicleSearch),
                ],
            ]),
        ]);
    }

    public function exportExcel(Request $request)
    {
        $searches = $this->applyFilters(CustomerVehicleSearch::query(), $request)
            ->latest()
            ->get();

        return Excel::download(new class($searches) implements FromCollection, WithHeadings {
            private $searches;

            public function __construct($searches)
            {
                $this->searches = $searches;
            }

            public function collection()
            {
                return $this->searches->map(function ($search) {
                    return [
                        'ID' => $search->id,
                        'Customer Name' => $search->customer_name ?? 'N/A',
                        'Customer Phone' => $search->customer_phone ?? 'N/A',
                        'Registration Number' => $search->registration_number,
                        'Charge Paid' => $search->paid_amount,
                        'Status' => $search->is_success ? 'Success' : 'Failed',
                        'Date' => $search->created_at->format('Y-m-d H:i:s'),
                    ];
                });
            }

            public function headings(): array
            {
                return [
                    'ID',
                    'Customer Name',
                    'Customer Phone',
                    'Registration Number',
                    'Charge Paid',
                    'Status',
                    'Date',
                ];
            }
        }, 'customer-rc-searches-' . date('Y-m-d') . '.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $searches = $this->applyFilters(CustomerVehicleSearch::query(), $request)
            ->latest()
            ->get();
        $totalRevenue = $searches->where('is_success', true)->sum('paid_amount');

        $pdf = Pdf::loadView('admin.customer-vehicle-searches.pdf', compact('searches', 'totalRevenue'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('customer-rc-searches-' . date('Y-m-d') . '.pdf');
    }

    public function downloadPdf(CustomerVehicleSearch $vehicleSearch)
    {
        $pdf = Pdf::loadView('admin.customer-vehicle-searches.single-pdf', compact('vehicleSearch'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('customer-rc-search-'.$vehicleSearch->registration_number.'.pdf');
    }

    private function applyFilters($query, Request $request)
    {
        if ($request->filled('search')) {
            $searchTerm = (string) $request->search;
            $query->where(function ($nestedQuery) use ($searchTerm) {
                $nestedQuery->where('registration_number', 'like', '%'.strtoupper($searchTerm).'%')
                    ->orWhere('customer_name', 'like', '%'.$searchTerm.'%')
                    ->orWhere('customer_phone', 'like', '%'.$searchTerm.'%');
            });
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

        return $query;
    }

    private function filters(Request $request): array
    {
        return [
            'search' => (string) $request->query('search', ''),
            'status' => (string) $request->query('status', ''),
            'from_date' => (string) $request->query('from_date', ''),
            'to_date' => (string) $request->query('to_date', ''),
        ];
    }

    private function mapSearch(CustomerVehicleSearch $search): array
    {
        return [
            'id' => $search->id,
            'customer_name' => $search->customer_name,
            'customer_phone' => $search->customer_phone,
            'registration_number' => $search->registration_number,
            'paid_amount' => (float) ($search->paid_amount ?? 0),
            'is_success' => (bool) $search->is_success,
            'is_refunded' => (bool) $search->is_refunded,
            'error_message' => $search->error_message,
            'created_at' => $this->formatDateTime($search->created_at),
            'actions' => [
                'show' => route('admin.customer-vehicle-searches.show', $search),
                'pdf' => route('admin.customer-vehicle-searches.downloadPdf', $search),
            ],
        ];
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
}
