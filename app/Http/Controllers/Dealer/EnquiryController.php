<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use Illuminate\Http\Request;

class EnquiryController extends Controller
{
    public function index(Request $request)
    {
        $dealer = auth('dealer')->user();
        $query = $dealer->enquiries()->with('car');

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $enquiries = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('dealer.enquiries.index', compact('enquiries'));
    }

    public function show(Enquiry $enquiry)
    {
        $dealer = auth('dealer')->user();
        if ($enquiry->dealer_id !== $dealer->id) {
            abort(403);
        }

        $enquiry->update(['status' => 'contacted']);

        return view('dealer.enquiries.show', compact('enquiry'));
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
}
