<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dealer;
use App\Models\PaymentLink;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentLinkController extends Controller
{
    public function index()
    {
        $paymentLinks = PaymentLink::with('dealer')
            ->latest()
            ->paginate(15)
            ->through(fn (PaymentLink $link) => [
                'id' => $link->id,
                'created_date' => optional($link->created_at)->format('d M Y'),
                'created_time' => optional($link->created_at)->format('h:i A'),
                'payee_name' => $link->dealer
                    ? ($link->dealer->company_name ?: $link->dealer->name)
                    : ($link->customer_name ?: 'Unknown Customer'),
                'payee_detail' => $link->dealer
                    ? trim(($link->dealer->name ?: 'Dealer').' '.($link->dealer->phone ? '('.$link->dealer->phone.')' : ''))
                    : trim(($link->customer_mobile ?: '').' '.($link->customer_email ? '('.$link->customer_email.')' : '')),
                'amount' => (float) $link->amount,
                'purpose' => $link->purpose,
                'gateway' => $link->gateway,
                'status' => $link->status,
                'expires_at' => optional($link->expires_at)->format('d M Y, h:i A'),
                'public_url' => route('pay.link', $link->id),
                'refresh_url' => route('admin.payment-links.refresh', $link->id),
                'delete_url' => route('admin.payment-links.destroy', $link->id),
            ]);

        $dealers = Dealer::where('status', 'approved')
            ->orderBy('company_name')
            ->orderBy('name')
            ->get(['id', 'name', 'company_name', 'phone'])
            ->map(fn (Dealer $dealer) => [
                'id' => $dealer->id,
                'label' => ($dealer->company_name ?: $dealer->name).($dealer->phone ? ' ('.$dealer->phone.')' : ''),
            ]);

        return Inertia::render('Admin/Finance/PaymentLinks', [
            'paymentLinks' => $paymentLinks,
            'dealers' => $dealers,
        ]);
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

    public function refresh(PaymentLink $paymentLink, \App\Services\PhonePeService $phonepeService)
    {
        if ($paymentLink->status !== 'pending') {
            return redirect()->back()->with('error', 'Payment link is already ' . $paymentLink->status);
        }

        if (!$paymentLink->transaction_id || !str_starts_with($paymentLink->transaction_id, 'PP_LNK_')) {
            return redirect()->back()->with('error', 'No PhonePe payment attempt has been made for this link yet. (Waiting for customer to click pay)');
        }

        try {
            $status = $phonepeService->verifyPaymentStatus($paymentLink->transaction_id);
            if ($status['success']) {
                $dealer = $paymentLink->dealer;
                $payment = $phonepeService->processPayment(
                    $dealer,
                    $paymentLink->transaction_id,
                    (float) $paymentLink->amount,
                    'custom_payment_link'
                );

                \Illuminate\Support\Facades\DB::transaction(function() use ($paymentLink, $payment) {
                    $paymentLink->update([
                        'status' => 'paid',
                        'transaction_id' => $payment->id
                    ]);
                });
                
                return redirect()->back()->with('success', 'Payment status synced! Marked as Paid.');
            }
            return redirect()->back()->with('info', 'Payment is still pending or failed at the gateway. PhonePe Status: ' . ($status['status'] ?? 'UNKNOWN'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to sync status: ' . $e->getMessage());
        }
    }
}
