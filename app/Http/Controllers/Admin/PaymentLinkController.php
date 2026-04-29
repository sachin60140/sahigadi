<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dealer;
use App\Models\PaymentLink;
use Illuminate\Http\Request;

class PaymentLinkController extends Controller
{
    public function index()
    {
        $paymentLinks = PaymentLink::with('dealer')->latest()->paginate(15);
        $dealers = Dealer::where('status', 'approved')->get();
        return view('admin.payment-links.index', compact('paymentLinks', 'dealers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'dealer_id' => 'nullable|exists:dealers,id',
            'customer_name' => 'nullable|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_mobile' => 'nullable|string|max:20',
            'amount' => 'required|numeric|min:1',
            'purpose' => 'required|string|max:255',
            'gateway' => 'required|in:razorpay,phonepe,any',
        ]);

        $paymentLink = PaymentLink::create([
            'dealer_id' => $request->dealer_id,
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_mobile' => $request->customer_mobile,
            'amount' => $request->amount,
            'purpose' => $request->purpose,
            'gateway' => $request->gateway,
            'status' => 'pending',
            // Default expiry: 7 days from now
            'expires_at' => now()->addDays(7),
        ]);

        return redirect()->back()->with('success', 'Payment Link created successfully! Share the generated link with the dealer.');
    }

    public function destroy(PaymentLink $paymentLink)
    {
        $paymentLink->delete();
        return redirect()->back()->with('success', 'Payment link deleted.');
    }
}
