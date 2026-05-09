<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ChallanPdfSearch;
use App\Services\ChallanPdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChallanPdfController extends Controller
{
    protected $challanPdfService;

    public function __construct(ChallanPdfService $challanPdfService)
    {
        $this->challanPdfService = $challanPdfService;
    }

    public function index()
    {
        return view('frontend.customer.challan_pdf.index');
    }

    public function search(Request $request)
    {
        $request->validate([
            'vehicle_number' => 'required|string|max:20'
        ]);

        $customer = Auth::guard('customer')->user();
        
        $result = $this->challanPdfService->fetchChallanPdf($request->vehicle_number, $customer, 'customer');

        if (isset($result['redirect_to_recharge']) && $result['redirect_to_recharge']) {
            return redirect()->route('customer.wallet.add')->with('error', $result['message']);
        }

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return redirect()->route('customer.challan-pdf.history')->with('success', 'Challan PDF generated successfully.');
    }

    public function history()
    {
        $customer = Auth::guard('customer')->user();
        $history = ChallanPdfSearch::where('customer_id', $customer->id)->latest()->paginate(15);
        
        return view('frontend.customer.challan_pdf.history', compact('history'));
    }
}
