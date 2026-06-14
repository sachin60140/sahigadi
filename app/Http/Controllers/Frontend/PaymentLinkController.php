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
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

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
            return Inertia::render('Public/PaymentLinks/Status', [
                'status' => $payment_link->status,
                'purpose' => $payment_link->purpose,
                'homeUrl' => route('home'),
            ]);
        }

        $payment_link->load('dealer');
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
            session()->put('payment_link_razorpay_orders.'.$payment_link->id, $orderData['order_id']);
        }

        return Inertia::render('Public/PaymentLinks/Checkout', [
            'link' => [
                'id' => $payment_link->id,
                'amount' => (float) $payment_link->amount,
                'purpose' => $payment_link->purpose,
                'gateway' => $payment_link->gateway,
                'payee' => $payment_link->dealer
                    ? ($payment_link->dealer->company_name ?? $payment_link->dealer->name)
                    : $payment_link->customer_name,
                'customer' => [
                    'name' => $payment_link->dealer?->name ?? $payment_link->customer_name,
                    'email' => $payment_link->dealer?->email ?? $payment_link->customer_email,
                    'phone' => $payment_link->dealer?->phone ?? $payment_link->customer_mobile,
                ],
                'expires_at' => $payment_link->expires_at?->toIso8601String(),
            ],
            'order' => $orderData,
            'keyId' => $this->razorpayService->getKeyId(),
            'isRazorpayActive' => $isRazorpayActive,
            'isPhonePeActive' => $isPhonePeActive,
            'checkoutUrl' => route('pay.link.checkout', $payment_link),
        ]);
    }

    public function checkout(Request $request, PaymentLink $payment_link)
    {
        if ($payment_link->status !== 'pending' || $payment_link->expires_at->isPast()) {
            return redirect()->route('pay.link', $payment_link->id)->with('error', 'Payment link is no longer valid.');
        }

        try {
            $validated = $request->validate([
                'gateway' => ['required', Rule::in(['phonepe', 'razorpay'])],
                'razorpay_order_id' => ['required_if:gateway,razorpay', 'nullable', 'string'],
                'razorpay_payment_id' => ['required_if:gateway,razorpay', 'nullable', 'string'],
                'razorpay_signature' => ['required_if:gateway,razorpay', 'nullable', 'string'],
            ]);
            $gateway = $validated['gateway'];

            if ($payment_link->gateway !== 'any' && $payment_link->gateway !== $gateway) {
                throw new Exception('This payment link does not support the selected gateway.');
            }

            if ($gateway === 'phonepe' && ! Setting::isPhonePeActive()) {
                throw new Exception('PhonePe is currently unavailable.');
            }

            if ($gateway === 'razorpay' && ! Setting::isRazorpayActive()) {
                throw new Exception('Razorpay is currently unavailable.');
            }

            if ($gateway === 'phonepe') {
                $transactionId = 'PP_LNK_'.Str::uuid().'_'.$payment_link->id;

                $payment_link->update(['transaction_id' => $transactionId]);

                session()->put('phonepe_link_payment', [
                    'transaction_id' => $transactionId,
                    'link_id' => $payment_link->id,
                ]);

                $response = $this->phonepeService->createPayment(
                    (float) $payment_link->amount,
                    $transactionId,
                    [
                        'payment_link_id' => $payment_link->id,
                        'redirectUrl' => route('pay.link.phonepe.callback'),
                    ]
                );

                if ($response['success']) {
                    return redirect()->away($response['redirect_url']);
                }

                throw new Exception('PhonePe payment initiation failed.');
            } elseif ($gateway === 'razorpay') {
                $expectedOrderId = session('payment_link_razorpay_orders.'.$payment_link->id);
                if (! $expectedOrderId || ! hash_equals((string) $expectedOrderId, (string) $request->razorpay_order_id)) {
                    throw new Exception('Payment order does not match this payment link.');
                }

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
                session()->forget('payment_link_razorpay_orders.'.$payment_link->id);

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

            $transactionId = (string) $paymentInfo['transaction_id'];
            if (! $payment_link->transaction_id
                || ! hash_equals((string) $payment_link->transaction_id, $transactionId)) {
                throw new Exception('Payment transaction does not match this payment link.');
            }

            $payment = $this->phonepeService->processPayment(
                $dealer,
                $transactionId,
                (float) $payment_link->amount,
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
        DB::transaction(function () use ($link, $paymentId) {
            $lockedLink = PaymentLink::query()
                ->whereKey($link->getKey())
                ->lockForUpdate()
                ->first();

            if (! $lockedLink || $lockedLink->status !== 'pending') {
                return;
            }

            $lockedLink->update([
                'status' => 'paid',
                'transaction_id' => $paymentId,
            ]);
        });
    }
}
