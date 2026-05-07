<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CustomerMahindraServiceHistory;
use App\Services\CustomerMahindraServiceHistoryService;
use App\Services\RazorpayService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class MahindraServiceHistoryController extends Controller
{
    protected CustomerMahindraServiceHistoryService $serviceHistoryService;

    protected RazorpayService $razorpayService;

    public function __construct(
        CustomerMahindraServiceHistoryService $serviceHistoryService,
        RazorpayService $razorpayService
    ) {
        $this->serviceHistoryService = $serviceHistoryService;
        $this->razorpayService = $razorpayService;
    }

    public function index()
    {
        $charge = $this->serviceHistoryService->getCharge();
        $history = collect();

        if (auth('customer')->check()) {
            $history = CustomerMahindraServiceHistory::where('customer_phone', auth('customer')->user()->phone)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('frontend.mahindra-service-history.index', compact('charge', 'history'));
    }

    public function search(Request $request)
    {
        $rules = [
            'vehicle_number' => 'required|string|min:4|max:20',
        ];

        if (!auth('customer')->check()) {
            $rules['customer_name'] = 'required|string|max:255';
            $rules['customer_phone'] = 'required|string|max:20';
            $rules['customer_email'] = 'nullable|email|max:255';
        }

        $request->validate($rules);

        $vehicleNumber = $request->vehicle_number;
        
        if (auth('customer')->check()) {
            $customerInfo = [
                'name' => auth('customer')->user()->name,
                'phone' => auth('customer')->user()->phone,
                'email' => auth('customer')->user()->email,
            ];
        } else {
            $customerInfo = [
                'name' => $request->customer_name,
                'phone' => $request->customer_phone,
                'email' => $request->customer_email,
            ];
        }

        // Cache disabled per user request: "search always fresh no old record required"

        $charge = $this->serviceHistoryService->getCharge();

        if (auth('customer')->check()) {
            $customer = auth('customer')->user();
            $wallet = \App\Models\CustomerWallet::firstOrCreate(
                ['customer_id' => $customer->id],
                ['balance' => 0]
            );

            if ($wallet->balance < $charge) {
                return redirect()->route('customer.wallet.add')
                    ->with('error', 'Low Balance! Please recharge your wallet to continue.');
            }

            // Deduct funds
            $transaction = $wallet->deductFunds($charge, "Mahindra Service History Search for {$vehicleNumber}");

            // Perform API search directly
            $result = $this->serviceHistoryService->search($vehicleNumber, $customerInfo);

            if (isset($result['data']) && $result['data']) {
                $result['data']->update([
                    'paid_amount' => $charge,
                ]);

                if ($result['success']) {
                    $result['data']->load('records');
                }
            }

            // If API fails, refund the wallet
            if (!$result['success']) {
                $wallet->addFunds($charge, "Refund: Failed Mahindra Service History Search for {$vehicleNumber}");
                $transaction->update(['remark' => $transaction->remark . ' (Refunded)']);
            }

            return view('frontend.mahindra-service-history.result', [
                'mahindraServiceHistory' => $result['data'] ?? null,
                'cached' => $result['cached'] ?? false,
                'success' => $result['success'],
                'message' => $result['message'],
            ]);
        }

        // For non-logged-in users, continue with Razorpay
        $order = $this->razorpayService->createOrder(
            $charge,
            'msh_'.time(),
            [
                'vehicle_number' => $vehicleNumber,
                'customer_name' => $customerInfo['name'],
                'customer_phone' => $customerInfo['phone'],
            ]
        );

        session([
            'mahindra_service_history_pending' => [
                'vehicle_number' => $vehicleNumber,
                'customer_info' => $customerInfo,
                'order_id' => $order['order_id'],
            ],
        ]);

        return view('frontend.mahindra-service-history.payment', [
            'orderId' => $order['order_id'],
            'amount' => $order['amount'],
            'keyId' => $this->razorpayService->getKeyId(),
            'vehicleNumber' => $vehicleNumber,
            'customerName' => $customerInfo['name'],
        ]);
    }

    public function paymentCallback(Request $request)
    {
        $pending = session('mahindra_service_history_pending');

        if (! $pending) {
            return redirect()->route('mahindra-service-history.index')->with('error', 'No pending payment found');
        }

        $razorpayOrderId = $request->razorpay_order_id ?? $request->query('razorpay_order_id');
        $razorpayPaymentId = $request->razorpay_payment_id ?? $request->query('razorpay_payment_id');
        $razorpaySignature = $request->razorpay_signature ?? $request->query('razorpay_signature');

        if (empty($razorpayOrderId) || empty($razorpayPaymentId) || empty($razorpaySignature)) {
            return redirect()->route('mahindra-service-history.index')->with('error', 'Payment was not completed');
        }

        if (! $this->razorpayService->verifySignature($razorpayOrderId, $razorpayPaymentId, $razorpaySignature)) {
            return redirect()->route('mahindra-service-history.index')->with('error', 'Payment verification failed');
        }

        $result = $this->serviceHistoryService->search(
            $pending['vehicle_number'],
            $pending['customer_info']
        );

        if (isset($result['data']) && $result['data']) {
            $result['data']->update([
                'razorpay_order_id' => $razorpayOrderId,
                'razorpay_payment_id' => $razorpayPaymentId,
            ]);

            if ($result['success']) {
                $result['data']->load('records');
            }
        }

        session()->forget('mahindra_service_history_pending');

        return view('frontend.mahindra-service-history.result', [
            'mahindraServiceHistory' => $result['data'],
            'cached' => $result['cached'] ?? false,
            'success' => $result['success'],
            'message' => $result['message'],
        ]);
    }

    public function show(CustomerMahindraServiceHistory $mahindraServiceHistory)
    {
        $mahindraServiceHistory->load('records');

        return view('frontend.mahindra-service-history.show', compact('mahindraServiceHistory'));
    }

    public function downloadPdf(CustomerMahindraServiceHistory $mahindraServiceHistory)
    {
        $mahindraServiceHistory->load('records');

        $pdf = Pdf::loadView('frontend.mahindra-service-history.pdf', compact('mahindraServiceHistory'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('mahindra-service-history-'.$mahindraServiceHistory->vehicle_number.'.pdf');
    }
}
