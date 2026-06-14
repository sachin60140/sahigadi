<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CustomerChallanSearch;
use App\Services\CustomerChallanSearchService;
use App\Services\RazorpayService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ChallanSearchController extends Controller
{
    protected CustomerChallanSearchService $challanService;

    protected RazorpayService $razorpayService;

    public function __construct(
        CustomerChallanSearchService $challanService,
        RazorpayService $razorpayService
    ) {
        $this->challanService = $challanService;
        $this->razorpayService = $razorpayService;
    }

    public function index()
    {
        $charge = $this->challanService->getCharge();

        return Inertia::render('Public/Services/Lookup', [
            'service' => [
                'key' => 'challan-search',
                'eyebrow' => 'Traffic compliance',
                'title' => 'E-Challan status check',
                'description' => 'See pending traffic challans, violation details and payable amounts before buying or selling.',
                'numberField' => 'vehicle_number',
                'numberLabel' => 'Vehicle registration number',
                'placeholder' => 'e.g. BR01AB1234',
                'submitLabel' => 'Check challan status',
                'actionUrl' => route('challan-search.search'),
                'charge' => (float) $charge,
                'requiresGuestDetails' => true,
                'customerAuthenticated' => false,
                'seoTitle' => 'E-Challan Check - SAHI GADI',
                'seoDescription' => 'Check pending e-challans online with SAHI GADI before buying or selling a used car. Search by vehicle number and review challan status.',
                'canonical' => route('challan-search.index'),
                'features' => [
                    'Pending challan count and total',
                    'Violation, location and status details',
                    'Quick vehicle-number based verification',
                ],
            ],
            'history' => [],
        ]);
    }

    public function search(Request $request)
    {
        $request->validate([
            'vehicle_number' => 'required|string|min:4|max:20',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
        ]);

        $vehicleNumber = $request->vehicle_number;
        $customerInfo = [
            'name' => $request->customer_name,
            'phone' => $request->customer_phone,
            'email' => $request->customer_email,
        ];

        $cached = CustomerChallanSearch::checkCache($vehicleNumber);
        if ($cached) {
            return Inertia::render('Public/Services/ChallanResult', [
                'challanSearch' => $cached,
                'cached' => true,
                'success' => true,
                'message' => 'Found in cache',
                'indexUrl' => route('challan-search.index'),
            ]);
        }

        $charge = $this->challanService->getCharge();

        $order = $this->razorpayService->createOrder(
            $charge,
            'ch_'.time(),
            [
                'vehicle_number' => $vehicleNumber,
                'customer_name' => $customerInfo['name'],
                'customer_phone' => $customerInfo['phone'],
            ]
        );

        session([
            'challan_search_pending' => [
                'vehicle_number' => $vehicleNumber,
                'customer_info' => $customerInfo,
                'order_id' => $order['order_id'],
            ],
        ]);

        return Inertia::render('Public/Services/Payment', [
            'orderId' => $order['order_id'],
            'amount' => (float) $order['amount'],
            'keyId' => $this->razorpayService->getKeyId(),
            'vehicleNumber' => $vehicleNumber,
            'customerName' => $customerInfo['name'],
            'reportLabel' => 'E-Challan Report',
            'description' => 'E-Challan Check - '.$vehicleNumber,
            'callbackUrl' => route('challan-search.callback'),
            'cancelUrl' => route('challan-search.index'),
        ]);
    }

    public function paymentCallback(Request $request)
    {
        $pending = session('challan_search_pending');

        if (! $pending) {
            return redirect()->route('challan-search.index')->with('error', 'No pending payment found');
        }

        $razorpayOrderId = $request->razorpay_order_id;
        $razorpayPaymentId = $request->razorpay_payment_id;
        $razorpaySignature = $request->razorpay_signature;

        if (empty($razorpayOrderId) || empty($razorpayPaymentId) || empty($razorpaySignature)) {
            return redirect()->route('challan-search.index')->with('error', 'Payment was not completed');
        }

        if (! hash_equals((string) $pending['order_id'], (string) $razorpayOrderId)) {
            return redirect()->route('challan-search.index')->with('error', 'Payment order does not match this search');
        }

        if (! $this->razorpayService->verifySignature($razorpayOrderId, $razorpayPaymentId, $razorpaySignature)) {
            return redirect()->route('challan-search.index')->with('error', 'Payment verification failed');
        }

        $result = $this->challanService->search(
            $pending['vehicle_number'],
            $pending['customer_info']
        );

        if (isset($result['data']) && $result['data']) {
            $result['data']->update([
                'razorpay_order_id' => $razorpayOrderId,
                'razorpay_payment_id' => $razorpayPaymentId,
            ]);
        }

        session()->forget('challan_search_pending');

        return Inertia::render('Public/Services/ChallanResult', [
            'challanSearch' => $result['data'] ?? null,
            'cached' => $result['cached'] ?? false,
            'success' => $result['success'],
            'message' => $result['message'],
            'indexUrl' => route('challan-search.index'),
        ]);
    }
}
