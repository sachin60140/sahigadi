<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerChallanSearch;
use App\Models\CustomerMarutiServiceHistory;
use App\Models\CustomerServiceHistory;
use App\Models\CustomerVehicleSearch;
use App\Services\RazorpayService;
use Illuminate\Http\Request;

class CustomerTransactionController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type', 'vahan');
        
        if ($type === 'challan') {
            $transactions = CustomerChallanSearch::orderBy('created_at', 'desc')->paginate(20);
        } elseif ($type === 'maruti') {
            $transactions = CustomerMarutiServiceHistory::orderBy('created_at', 'desc')->paginate(20);
        } elseif ($type === 'mahindra') {
            $transactions = CustomerServiceHistory::orderBy('created_at', 'desc')->paginate(20);
        } else {
            $type = 'vahan';
            $transactions = CustomerVehicleSearch::orderBy('created_at', 'desc')->paginate(20);
        }

        return view('admin.customer-transactions.index', compact('transactions', 'type'));
    }

    protected function getModelInstance(string $type, $id)
    {
        if ($type === 'challan') {
            return CustomerChallanSearch::findOrFail($id);
        } elseif ($type === 'maruti') {
            return CustomerMarutiServiceHistory::findOrFail($id);
        } elseif ($type === 'mahindra') {
            return CustomerServiceHistory::findOrFail($id);
        } else {
            return CustomerVehicleSearch::findOrFail($id);
        }
    }

    public function show(Request $request, $id)
    {
        $type = $request->query('type', 'vahan');
        $transaction = $this->getModelInstance($type, $id);
        
        return view('admin.customer-transactions.show', compact('transaction', 'type'));
    }

    public function refund(Request $request, $id, RazorpayService $razorpayService)
    {
        $type = $request->query('type', 'vahan');
        $transaction = $this->getModelInstance($type, $id);

        if ($transaction->is_success) {
            // Uncomment next line if refunds should be strictly for failed only
            // return redirect()->back()->with('error', 'Cannot refund a successful API request.');
        }

        if ($transaction->is_refunded) {
            return redirect()->back()->with('error', 'Payment has already been refunded.');
        }

        if (!$transaction->razorpay_payment_id) {
            return redirect()->back()->with('error', 'No Razorpay payment ID found for this transaction.');
        }

        $result = $razorpayService->refundPayment($transaction->razorpay_payment_id);

        if ($result['success']) {
            $transaction->is_refunded = true;
            $transaction->razorpay_refund_id = $result['refund_id'];
            $transaction->save();

            return redirect()->back()->with('success', 'Refund initiated successfully (ID: ' . $result['refund_id'] . ').');
        } else {
            return redirect()->back()->with('error', 'Refund failed: ' . $result['message']);
        }
    }
}
