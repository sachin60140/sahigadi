<?php

namespace App\Http\Controllers\Admin;

use App\Exports\WalletRechargesExport;
use App\Http\Controllers\Controller;
use App\Models\WalletTransaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class WalletRechargeController extends Controller
{
    private function buildQuery(Request $request)
    {
        $query = WalletTransaction::with(['wallet.dealer'])
            ->where('type', 'credit')
            ->where(function($q) {
                $q->where('remark', 'LIKE', '%recharge%')
                  ->orWhere('reference_type', 'payment')
                  ->orWhere('reference_type', 'admin_credit');
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
                          $q2->whereColumn('payments.razorpay_payment_id', 'wallet_transactions.reference_id')
                             ->orWhereColumn('payments.phonepe_transaction_id', 'wallet_transactions.reference_id');
                      });
                });
            }
        }

        return $query;
    }

    public function index(Request $request)
    {
        $transactions = $this->buildQuery($request)->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.wallet-recharges.index', compact('transactions'));
    }

    public function exportExcel(Request $request)
    {
        $transactions = $this->buildQuery($request)->orderBy('created_at', 'desc')->get();
        return Excel::download(new WalletRechargesExport($transactions), 'wallet-recharges-'.date('Y-m-d').'.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $transactions = $this->buildQuery($request)->orderBy('created_at', 'desc')->get();
        $pdf = Pdf::loadView('admin.wallet-recharges.pdf', compact('transactions'));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('wallet-recharges-'.date('Y-m-d').'.pdf');
    }

    public function downloadReceipt($id)
    {
        $transaction = WalletTransaction::with('wallet.dealer')->findOrFail($id);

        abort_if($transaction->type !== 'credit', 403, 'Only credit transactions have a receipt.');

        $data = [
            'transaction' => $transaction,
            'dealer' => $transaction->wallet->dealer,
            'baseAmount' => $transaction->amount,
            'gstAmount' => $transaction->amount * 0.18,
            'totalAmount' => $transaction->amount * 1.18,
            'date' => $transaction->created_at->format('d M Y')
        ];

        $pdf = Pdf::loadView('dealer.wallet.receipt-pdf', $data);
        return $pdf->download('Admin-Wallet-Recharge-Receipt-'.$transaction->id.'.pdf');
    }
}
