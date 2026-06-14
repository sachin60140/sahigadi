<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Models\ChallanPdfSearch;
use App\Services\ChallanPdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;
use Inertia\Inertia;

class ChallanPdfController extends Controller
{
    protected $challanPdfService;

    public function __construct(ChallanPdfService $challanPdfService)
    {
        $this->challanPdfService = $challanPdfService;
    }

    public function index()
    {
        return $this->renderPage(ChallanPdfSearch::where('dealer_id', Auth::guard('dealer')->id())->latest()->paginate(15));
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
        
        return $this->renderPage($history);
    }

    private function renderPage($history)
    {
        $dealer = Auth::guard('dealer')->user();

        return Inertia::render('Dealer/Services/ChallanPdf', [
            'history' => $history->through(fn (ChallanPdfSearch $item) => [
                'id' => $item->id, 'vehicle_number' => $item->vehicle_number, 'is_success' => (bool) $item->is_success, 'charge' => (float) ($item->charge_amount ?? 0), 'error' => $item->error_message, 'pdf_url' => $item->pdf_url, 'created_at' => optional($item->created_at)->format('d M Y, h:i A'),
            ]),
            'charge' => (float) Setting::getDealerChallanPdfCharge(),
            'walletBalance' => $dealer->walletBalance(),
            'active' => Setting::isChallanPdfActive(),
            'actions' => ['search' => route('dealer.challan-pdf.search'), 'history' => route('dealer.challan-pdf.history'), 'wallet' => route('dealer.wallet.add')],
        ]);
    }
}
