<?php

namespace App\Http\Controllers\Admin;

use App\Exports\CustomerWalletRechargesExport;
use App\Http\Controllers\Controller;
use App\Models\CustomerWalletTransaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CustomerWalletRechargeController extends Controller
{
    private function buildQuery(Request $request)
    {
        $query = CustomerWalletTransaction::with(['wallet.customer'])
            ->where(function($q) {
                $q->where(function($q1) {
                    $q1->where('type', 'credit')
                       ->where(function($q2) {
                           $q2->where('remark', 'LIKE', '%recharge%')
                             ->orWhere('reference_type', 'payment')
                             ->orWhere('reference_type', 'admin_credit');
                       });
                })->orWhere(function($q3) {
                    $q3->where('type', 'debit')
                       ->where('reference_type', 'admin_debit');
                });
            });

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        if ($request->filled('payment_gateway')) {
            $gateway = $request->payment_gateway;
            if ($gateway === 'direct_deposit') {
                $query->where('reference_type', 'admin_credit');
            } else {
                $query->whereExists(function($q) use ($gateway) {
                    $q->select(\Illuminate\Support\Facades\DB::raw(1))
                      ->from('payments')
                      ->where('payment_gateway', $gateway)
                      ->where(function($q2) {
                          $q2->whereColumn('payments.razorpay_payment_id', 'customer_wallet_transactions.reference_id')
                             ->orWhereColumn('payments.phonepe_transaction_id', 'customer_wallet_transactions.reference_id');
                      });
                });
            }
        }

        if ($request->filled('search')) {
            $query->whereHas('wallet.customer', function($q) use ($request) {
                $q->where('customer_unique_id', 'like', '%'.$request->search.'%')
                  ->orWhere('name', 'like', '%'.$request->search.'%')
                  ->orWhere('email', 'like', '%'.$request->search.'%')
                  ->orWhere('phone', 'like', '%'.$request->search.'%');
            });
        }

        return $query;
    }

    public function index(Request $request)
    {
        $transactions = $this->buildQuery($request)->orderBy('created_at', 'desc')->paginate(20);
        $customers = \App\Models\Customer::orderBy('name')->get();
        return view('admin.customer-wallet-recharges.index', compact('transactions', 'customers'));
    }

    public function deductMoney(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'amount' => 'required|numeric|min:1',
            'remark' => 'required|string|max:255',
        ]);

        $customer = \App\Models\Customer::findOrFail($request->customer_id);
        $wallet = $customer->wallet()->firstOrCreate([]);

        if ($wallet->balance < $request->amount) {
            return back()->with('error', 'Insufficient balance in customer wallet. Current balance: ₹' . number_format($wallet->balance, 2));
        }

        $wallet->deductFunds(
            $request->amount,
            $request->remark,
            'admin_debit_' . time(),
            'admin_debit'
        );

        return back()->with('success', 'Amount deducted from customer wallet successfully');
    }

    public function exportExcel(Request $request)
    {
        // For now, don't implement Excel export as it requires creating CustomerWalletRechargesExport
        return redirect()->back()->with('error', 'Excel export not implemented yet.');
    }

    public function exportPdf(Request $request)
    {
        $transactions = $this->buildQuery($request)->orderBy('created_at', 'desc')->get();
        $pdf = Pdf::loadView('admin.customer-wallet-recharges.pdf', compact('transactions'));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('customer-wallet-recharges-'.date('Y-m-d').'.pdf');
    }

    public function downloadReceipt($id)
    {
        $transaction = CustomerWalletTransaction::with('wallet.customer')->findOrFail($id);

        abort_if($transaction->type !== 'credit', 403, 'Only credit transactions have a receipt.');

        $data = [
            'transaction' => $transaction,
            'customer' => $transaction->wallet->customer,
            'baseAmount' => $transaction->amount,
            'gstAmount' => $transaction->amount * 0.18,
            'totalAmount' => $transaction->amount * 1.18,
            'date' => $transaction->created_at->format('d M Y')
        ];

        $pdf = Pdf::loadView('frontend.customer.wallet.receipt-pdf', $data);
        return $pdf->download('Admin-Customer-Wallet-Recharge-Receipt-'.$transaction->id.'.pdf');
    }
}
