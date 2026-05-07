<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerVehicleSearch;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CustomerVehicleSearchController extends Controller
{
    public function index(Request $request)
    {
        $query = CustomerVehicleSearch::query();

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('registration_number', 'like', '%' . strtoupper($searchTerm) . '%')
                  ->orWhere('customer_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('customer_phone', 'like', '%' . $searchTerm . '%');
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

        $searches = $query->orderBy('created_at', 'desc')->paginate(25);
        $totalRevenue = $searches->sum('paid_amount');

        return view('admin.customer-vehicle-searches.index', compact('searches', 'totalRevenue'));
    }

    public function show(CustomerVehicleSearch $vehicleSearch)
    {
        return view('admin.customer-vehicle-searches.show', compact('vehicleSearch'));
    }

    public function exportExcel(Request $request)
    {
        $query = CustomerVehicleSearch::query();

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('registration_number', 'like', '%' . strtoupper($searchTerm) . '%')
                  ->orWhere('customer_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('customer_phone', 'like', '%' . $searchTerm . '%');
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

        $searches = $query->orderBy('created_at', 'desc')->get();

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
        $query = CustomerVehicleSearch::query();

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('registration_number', 'like', '%' . strtoupper($searchTerm) . '%')
                  ->orWhere('customer_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('customer_phone', 'like', '%' . $searchTerm . '%');
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

        $searches = $query->orderBy('created_at', 'desc')->get();
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
}
