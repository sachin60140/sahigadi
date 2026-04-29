<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\PaymentLink;
use App\Models\Setting;
use App\Services\PhonePeService;
use App\Services\RazorpayService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentLinkController extends Controller
{
    public function __construct(
        protected RazorpayService $razorpayService,
        protected PhonePeService $phonepeService
    ) {}

    public function show(PaymentLink $payment_link)
    {
        if ($payment_link->status !== 'pending' || $payment_link->expires_at->isPast()) {
            if ($payment_link->status === 'pending' && $payment_link->expires_at->isPast()) {
                $payment_link->update(['status' => 'expired']);
            }
            return view('frontend.payment-links.expired', compact('payment_link'));
        }

        $isRazorpayActive = Setting::isRazorpayActive();
        $isPhonePeActive = Setting::isPhonePeActive();

        $orderData = null;
        if ($isRazorpayActive && in_array($payment_link->gateway, ['any', 'razorpay'])) {
            // Receipt must be <= 40 chars for Razorpay. UUID is 36, so 'pl_' + 36 = 39.
            $receipt = 'pl_' . $payment_link->id;
            $orderData = $this->razorpayService->createOrder($payment_link->amount, $receipt, [
                'payment_link_id' => $payment_link->id,
                'dealer_id' => $payment_link->dealer_id,
            ]);
        }

        return view('frontend.payment-links.show', [
            'link' => $payment_link,
            'order' => $orderData,
            'keyId' => $this->razorpayService->getKeyId(),
            'isRazorpayActive' => $isRazorpayActive,
            'isPhonePeActive' => $isPhonePeActive,
        ]);
    }

    public function checkout(Request $request, PaymentLink $payment_link)
    {
        if ($payment_link->status !== 'pending' || $payment_link->expires_at->isPast()) {
            return redirect()->route('pay.link', $payment_link->id)->with('error', 'Payment link is no longer valid.');
        }

        try {
            $gateway = $request->gateway;
            
            if ($gateway === 'phonepe') {
                $transactionId = 'PP_LNK_' . time() . '_' . $payment_link->id;

                $payment_link->update(['transaction_id' => $transactionId]);

                session()->put('phonepe_link_payment', [
                    'transaction_id' => $transactionId,
                    'link_id' => $payment_link->id,
                    'amount' => $payment_link->amount,
                ]);

                $response = $this->phonepeService->createPayment(
                    $payment_link->amount, 
                    $transactionId, 
                    ['payment_link_id' => $payment_link->id],
                    route('pay.link.phonepe.callback')
                );

                if ($response['success']) {
                    return redirect()->away($response['redirect_url']);
                }

                throw new Exception('PhonePe payment initiation failed.');
            } elseif ($gateway === 'razorpay') {
                $dealer = $payment_link->dealer;
                
                $payment = $this->razorpayService->processPayment(
                    $dealer,
                    $request->razorpay_order_id,
                    $request->razorpay_payment_id,
                    $request->razorpay_signature,
                    $payment_link->amount,
                    'custom_payment_link',
                    $payment_link->id
                );

                $this->fulfillLink($payment_link, $payment->id);

                return redirect()->route('pay.link', $payment_link->id)->with('success', 'Payment successful!');
            }

            return redirect()->back()->with('error', 'Invalid gateway selected.');
        } catch (Exception $e) {
            return redirect()->route('pay.link', $payment_link->id)->with('error', $e->getMessage());
        }
    }

    public function phonepeCallback(Request $request)
    {
        try {
            $paymentInfo = session()->get('phonepe_link_payment');

            if (!$paymentInfo) {
                throw new Exception('Payment information lost in session.');
            }

            $payment_link = PaymentLink::findOrFail($paymentInfo['link_id']);
            $dealer = $payment_link->dealer;
            
            if ($payment_link->status !== 'pending') {
                session()->forget('phonepe_link_payment');
                return redirect()->route('pay.link', $payment_link->id)->with('success', 'Payment already verified!');
            }

            $transactionId = $paymentInfo['transaction_id'];

            $payment = $this->phonepeService->processPayment(
                $dealer,
                $transactionId,
                $paymentInfo['amount'],
                'custom_payment_link'
            );

            $this->fulfillLink($payment_link, $payment->id);

            session()->forget('phonepe_link_payment');
            return redirect()->route('pay.link', $payment_link->id)->with('success', 'PhonePe Payment successful!');
        } catch (Exception $e) {
            if (isset($paymentInfo['link_id'])) {
                return redirect()->route('pay.link', $paymentInfo['link_id'])->with('error', $e->getMessage());
            }
            return redirect('/')->with('error', $e->getMessage());
        }
    }

    protected function fulfillLink(PaymentLink $link, $paymentId)
    {
        DB::beginTransaction();
        try {
            if ($link->status !== 'pending') {
                DB::rollBack();
                return;
            }

            $link->update([
                'status' => 'paid',
                'transaction_id' => $paymentId
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
