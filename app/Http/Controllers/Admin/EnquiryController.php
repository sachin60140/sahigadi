<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use Illuminate\Http\Request;

class EnquiryController extends Controller
{
    public function index(Request $request)
    {
        $query = Enquiry::with(['car', 'dealer']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('dealer_id')) {
            $query->where('dealer_id', $request->dealer_id);
        }

        $enquiries = $query->orderBy('created_at', 'desc')->paginate(25);

        return view('admin.enquiries.index', compact('enquiries'));
    }

    public function show(Enquiry $enquiry)
    {
        $enquiry->load(['car', 'dealer']);

        return view('admin.enquiries.show', compact('enquiry'));
    }

    public function markContacted(Enquiry $enquiry)
    {
        $enquiry->update(['status' => 'contacted']);

        return back()->with('success', 'Enquiry marked as contacted');
    }
}
