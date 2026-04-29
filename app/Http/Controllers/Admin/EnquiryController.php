<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use Illuminate\Http\Request;

class EnquiryController extends Controller
{
    public function index(Request $request)
    {
        $query = Enquiry::with(['car', 'customerCar', 'dealer']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('dealer_id')) {
            $query->where('dealer_id', $request->dealer_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('car_id')) {
            $query->where('car_id', $request->car_id);
        }

        $enquiries = $query->orderBy('created_at', 'desc')->paginate(25);

        $dealers = \App\Models\Dealer::orderBy('name')->get();

        return view('admin.enquiries.index', compact('enquiries', 'dealers'));
    }

    public function show(Enquiry $enquiry)
    {
        $enquiry->load(['car', 'customerCar', 'dealer']);

        return view('admin.enquiries.show', compact('enquiry'));
    }

    public function markContacted(Enquiry $enquiry)
    {
        $enquiry->update(['status' => 'contacted']);

        return back()->with('success', 'Enquiry marked as contacted');
    }

    public function exportExcel(Request $request)
    {
        $query = Enquiry::with(['car', 'customerCar', 'dealer']);

        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('dealer_id')) $query->where('dealer_id', $request->dealer_id);
        if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to')) $query->whereDate('created_at', '<=', $request->date_to);
        if ($request->filled('car_id')) $query->where('car_id', $request->car_id);
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        $enquiries = $query->orderBy('created_at', 'desc')->get();

        $filename = 'enquiries_export_' . date('Y-m-d') . '.csv';
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['ID', 'Date', 'Customer Name', 'Customer Phone', 'Car', 'Dealer', 'Status', 'IP Address'];

        $callback = function() use ($enquiries, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($enquiries as $enquiry) {
                fputcsv($file, [
                    $enquiry->id,
                    $enquiry->created_at->format('Y-m-d H:i:s'),
                    $enquiry->customer_name,
                    $enquiry->customer_phone,
                    $enquiry->actual_car ? $enquiry->actual_car->title : 'N/A',
                    $enquiry->dealer ? $enquiry->dealer->name : 'N/A',
                    ucfirst($enquiry->status),
                    $enquiry->ip_address ?? 'N/A'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
