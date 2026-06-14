<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EnquiryController extends Controller
{
    public function index(Request $request)
    {
        $dealer = auth('dealer')->user();
        $query = $dealer->enquiries()->with('car');

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $enquiries = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return Inertia::render('Dealer/Enquiries/Index', [
            'enquiries' => $enquiries->through(fn (Enquiry $enquiry) => $this->mapEnquiry($enquiry)),
            'filters' => [
                'status' => (string) $request->query('status', 'all'),
            ],
            'stats' => [
                'total' => $dealer->enquiries()->count(),
                'new' => $dealer->enquiries()->where('status', 'new')->count(),
                'contacted' => $dealer->enquiries()->where('status', 'contacted')->count(),
            ],
        ]);
    }

    public function show(Enquiry $enquiry)
    {
        $dealer = auth('dealer')->user();
        if ($enquiry->dealer_id !== $dealer->id) {
            abort(403);
        }

        $enquiry->update(['status' => 'contacted']);

        $enquiry->load('car');

        return Inertia::render('Dealer/Enquiries/Show', [
            'enquiry' => [
                ...$this->mapEnquiry($enquiry),
                'customer_email' => $enquiry->customer_email,
                'message' => $enquiry->message,
                'car_price' => (float) ($enquiry->car?->price ?? 0),
            ],
            'actions' => [
                'index' => route('dealer.enquiries.index'),
                'contacted' => route('dealer.enquiries.contacted', $enquiry),
            ],
        ]);
    }

    public function markContacted(Enquiry $enquiry)
    {
        $dealer = auth('dealer')->user();
        if ($enquiry->dealer_id !== $dealer->id) {
            abort(403);
        }

        $enquiry->update(['status' => 'contacted']);

        return back()->with('success', 'Enquiry marked as contacted');
    }

    private function mapEnquiry(Enquiry $enquiry): array
    {
        $digits = preg_replace('/[^0-9]/', '', $enquiry->customer_phone);

        return [
            'id' => $enquiry->id,
            'customer_name' => $enquiry->customer_name,
            'customer_phone' => $enquiry->customer_phone,
            'status' => $enquiry->status,
            'created_at' => optional($enquiry->created_at)->format('d M Y, h:i A'),
            'car_title' => $enquiry->car?->title,
            'car_url' => $enquiry->car?->slug ? route('car.detail', $enquiry->car->slug) : null,
            'show_url' => route('dealer.enquiries.show', $enquiry),
            'call_url' => 'tel:'.$enquiry->customer_phone,
            'whatsapp_url' => 'https://wa.me/'.$digits,
        ];
    }
}
