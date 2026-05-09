<?php

namespace App\Http\Controllers\Frontend\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Enquiry;

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

        return view('frontend.customer.enquiries', compact('enquiries'));
    }
}
