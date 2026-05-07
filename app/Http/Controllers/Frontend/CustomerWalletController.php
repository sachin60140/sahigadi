<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CustomerWalletTransaction;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;

class CustomerWalletController extends Controller
{
    public function index()
    {
        $customer = auth('customer')->user();
        $wallet = $customer->wallet()->firstOrCreate([]);
        $balance = $wallet->balance;
        $transactions = $wallet->transactions()->orderBy('created_at', 'desc')->paginate(20);
        $minRechargeAmount = Setting::getCustomerMinimumWalletRechargeAmount();

        return view('frontend.customer.wallet.index', compact('balance', 'transactions', 'minRechargeAmount'));
    }

    public function add()
    {
        return redirect()->route('customer.wallet.index')->with('open_recharge_modal', true);
    }

    public function downloadReceipt($id)
    {
        $customer = auth('customer')->user();
        $transaction = CustomerWalletTransaction::where('id', $id)
            ->whereHas('wallet', function ($q) use ($customer) {
                $q->where('customer_id', $customer->id);
            })
            ->firstOrFail();

        abort_if($transaction->type !== 'credit', 403, 'Only credit transactions have a receipt.');

        $data = [
            'transaction' => $transaction,
            'customer' => $customer,
            'baseAmount' => $transaction->amount,
            'gstAmount' => $transaction->amount * 0.18,
            'totalAmount' => $transaction->amount * 1.18,
            'date' => $transaction->created_at->format('d M Y')
        ];

        $pdf = Pdf::loadView('frontend.customer.wallet.receipt-pdf', $data);

        return $pdf->download('Customer-Wallet-Recharge-Receipt-'.$transaction->id.'.pdf');
    }
}
