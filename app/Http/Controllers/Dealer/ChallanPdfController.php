<?php

namespace App\Http\Controllers\Dealer;

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
        return view('dealer.challan_pdf.index');
    }

    public function search(Request $request)
    {
        $request->validate([
            'vehicle_number' => 'required|string|max:20'
        ]);

        $dealer = Auth::guard('dealer')->user();
        
        $result = $this->challanPdfService->fetchChallanPdf($request->vehicle_number, $dealer, 'dealer');

        if (isset($result['redirect_to_recharge']) && $result['redirect_to_recharge']) {
            return redirect()->route('dealer.wallet.add')->with('error', $result['message']);
        }

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return redirect()->route('dealer.challan-pdf.history')->with('success', 'Challan PDF generated successfully.');
    }

    public function history()
    {
        $dealer = Auth::guard('dealer')->user();
        $history = ChallanPdfSearch::where('dealer_id', $dealer->id)->latest()->paginate(15);
        
        return view('dealer.challan_pdf.history', compact('history'));
    }
}
