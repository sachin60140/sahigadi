<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dealer;
use App\Models\Enquiry;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EnquiryController extends Controller
{
    public function index(Request $request)
    {
        $query = Enquiry::with(['car.brand', 'customerCar.brand', 'dealer']);

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

        $enquiries = $query->orderBy('created_at', 'desc')->paginate(25)->withQueryString();
        $filters = [
            'search' => (string) $request->query('search', ''),
            'status' => (string) $request->query('status', ''),
            'dealer_id' => (string) $request->query('dealer_id', ''),
            'date_from' => (string) $request->query('date_from', ''),
            'date_to' => (string) $request->query('date_to', ''),
            'car_id' => (string) $request->query('car_id', ''),
        ];

        return Inertia::render('Admin/Enquiries/Index', [
            'enquiries' => $enquiries->through(fn (Enquiry $enquiry) => $this->mapEnquiry($enquiry)),
            'dealers' => Dealer::orderBy('name')->get()->map(fn (Dealer $dealer) => [
                'id' => $dealer->id,
                'name' => $dealer->name,
            ])->values(),
            'filters' => $filters,
            'stats' => [
                'total' => Enquiry::count(),
                'new' => Enquiry::where('status', 'new')->count(),
                'contacted' => Enquiry::where('status', 'contacted')->count(),
                'dealer' => Enquiry::whereNotNull('dealer_id')->count(),
            ],
            'exportUrl' => route('admin.enquiries.exportExcel', array_filter(
                $filters,
                fn ($value) => $value !== ''
            )),
        ]);
    }

    public function show(Enquiry $enquiry)
    {
        $enquiry->load(['car.brand', 'customerCar.brand', 'dealer']);

        return Inertia::render('Admin/Enquiries/Show', [
            'enquiry' => $this->mapEnquiry($enquiry, true),
        ]);
    }

    public function markContacted(Enquiry $enquiry)
    {
        $enquiry->update(['status' => 'contacted']);

        return back()->with('success', 'Enquiry marked as contacted');
    }

    public function exportExcel(Request $request)
    {
        $query = Enquiry::with(['car.brand', 'customerCar.brand', 'dealer']);

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

    private function mapEnquiry(Enquiry $enquiry, bool $includeDetail = false): array
    {
        $vehicle = $enquiry->actual_car;
        $vehicleData = null;

        if ($vehicle) {
            $isDealerCar = (bool) $enquiry->dealer_id;
            $vehicleData = [
                'id' => $vehicle->id,
                'type' => $isDealerCar ? 'dealer' : 'customer',
                'title' => $vehicle->title,
                'brand' => $vehicle->brand?->name,
                'year' => $vehicle->year,
                'fuel_type' => $vehicle->fuel_type,
                'km_driven' => $vehicle->km_driven,
                'price' => (float) ($vehicle->price ?? 0),
                'registration_number' => $vehicle->registration_number,
                'owner_name' => $isDealerCar ? null : $vehicle->owner_name,
                'admin_url' => $isDealerCar
                    ? route('admin.cars.show', $vehicle)
                    : route('admin.customer-listings.show', $vehicle),
                'public_url' => $vehicle->slug ? route('car.detail', $vehicle->slug) : null,
            ];
        }

        $carDetails = $vehicle
            ? ' '.$vehicle->title.' ('.trim(($vehicle->year ?? '').' '.ucfirst($vehicle->fuel_type ?? '')).')'
            : '';
        $whatsappText = "Hi {$enquiry->customer_name},\n\nThank you for your interest in the{$carDetails} listed on SAHI GADI!\n\nPlease let us know if you need any further details or would like to schedule a visit.";
        $cleanPhone = preg_replace('/[^0-9]/', '', (string) $enquiry->customer_phone);

        $data = [
            'id' => $enquiry->id,
            'customer_name' => $enquiry->customer_name,
            'customer_email' => $enquiry->customer_email,
            'customer_phone' => $enquiry->customer_phone,
            'status' => $enquiry->status,
            'created_at' => $this->formatDateTime($enquiry->created_at),
            'vehicle' => $vehicleData,
            'dealer' => $enquiry->dealer ? [
                'id' => $enquiry->dealer->id,
                'name' => $enquiry->dealer->name,
                'company_name' => $enquiry->dealer->company_name,
                'phone' => $enquiry->dealer->phone,
                'email' => $enquiry->dealer->email,
                'show_url' => route('admin.dealers.show', $enquiry->dealer),
            ] : null,
            'listed_by' => $enquiry->dealer_id ? 'dealer' : 'customer',
            'whatsapp_url' => $cleanPhone
                ? 'https://wa.me/'.$cleanPhone.'?text='.urlencode($whatsappText)
                : null,
            'actions' => [
                'show' => route('admin.enquiries.show', $enquiry),
                'contacted' => route('admin.enquiries.contacted', $enquiry),
            ],
        ];

        if ($includeDetail) {
            $data['message'] = $enquiry->message;
            $data['ip_address'] = $enquiry->ip_address;
            $data['phone_url'] = $enquiry->customer_phone ? 'tel:'.$enquiry->customer_phone : null;
            $data['email_url'] = $enquiry->customer_email ? 'mailto:'.$enquiry->customer_email : null;
        }

        return $data;
    }

    private function formatDateTime($value): ?string
    {
        if (! $value) {
            return null;
        }

        if ($value instanceof \Carbon\CarbonInterface) {
            return $value->format('d M Y, h:i A');
        }

        try {
            return \Carbon\Carbon::parse($value)->format('d M Y, h:i A');
        } catch (\Throwable) {
            return (string) $value;
        }
    }
}
