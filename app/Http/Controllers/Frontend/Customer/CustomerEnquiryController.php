<?php

namespace App\Http\Controllers\Frontend\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Enquiry;
use Inertia\Inertia;

class CustomerEnquiryController extends Controller
{
    public function index()
    {
        $customerListingIds = auth('customer')->user()->listings()->pluck('id');

        $enquiries = Enquiry::with('customerCar')
            ->whereNull('dealer_id')
            ->whereIn('car_id', $customerListingIds)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return Inertia::render('Customer/Enquiries/Index', [
            'enquiries' => $enquiries->through(fn (Enquiry $enquiry) => [
                'id' => $enquiry->id,
                'customer_name' => $enquiry->customer_name ?: 'Customer',
                'customer_phone' => $enquiry->customer_phone,
                'customer_email' => $enquiry->customer_email,
                'status' => $enquiry->status,
                'created_at' => optional($enquiry->created_at)->format('d M Y, h:i A'),
                'car' => $enquiry->customerCar ? [
                    'title' => $enquiry->customerCar->title,
                    'unique_id' => $enquiry->customerCar->unique_id,
                    'year' => $enquiry->customerCar->year,
                    'registration_number' => $enquiry->customerCar->registration_number,
                    'url' => route('car.detail', $enquiry->customerCar->slug),
                ] : null,
            ]),
        ]);
    }
}
