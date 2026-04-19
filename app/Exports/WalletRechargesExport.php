<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class WalletRechargesExport implements FromCollection, WithHeadings, WithMapping
{
    protected $transactions;

    public function __construct($transactions)
    {
        $this->transactions = $transactions;
    }

    public function collection()
    {
        return $this->transactions;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Receipt No',
            'Dealer Name',
            'Firm Name',
            'GST Number',
            'Razorpay Ref ID',
            'Base Amount (Rs)',
            'GST Amount (Rs)',
            'Total Paid (Rs)'
        ];
    }

    public function map($transaction): array
    {
        $base = $transaction->amount;
        $gst = $base * 0.18;
        $total = $base + $gst;
        
        $receipt = 'RCPT-' . $transaction->created_at->format('Y') . '-' . str_pad($transaction->id, 5, '0', STR_PAD_LEFT);
        $dealer = $transaction->wallet->dealer;

        return [
            $transaction->created_at->format('d M Y, h:i A'),
            $receipt,
            $dealer->name ?? 'N/A',
            $dealer->firm_name ?? 'N/A',
            $dealer->gst_number ?? 'N/A',
            $transaction->reference_id ?? 'N/A',
            number_format($base, 2, '.', ''),
            number_format($gst, 2, '.', ''),
            number_format($total, 2, '.', '')
        ];
    }
}
