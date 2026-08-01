<?php

namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

/**
 * Invoice register export, laid out to match the columns needed for GSTR-1
 * (B2B / B2C outward supplies).
 */
class InvoicesExport implements FromCollection, WithHeadings, WithMapping
{
    protected $invoices;

    public function __construct($invoices)
    {
        $this->invoices = $invoices;
    }

    public function collection()
    {
        return $this->invoices;
    }

    public function headings(): array
    {
        return [
            'Invoice Number',
            'Invoice Date',
            'Financial Year',
            'Buyer Name',
            'Buyer Company',
            'Buyer GSTIN',
            'Supply Type',
            'Place of Supply',
            'POS Code',
            'SAC',
            'Taxable Value (Rs)',
            'CGST Rate (%)',
            'CGST Amount (Rs)',
            'SGST Rate (%)',
            'SGST Amount (Rs)',
            'IGST Rate (%)',
            'IGST Amount (Rs)',
            'Total Tax (Rs)',
            'Invoice Total (Rs)',
            'Reverse Charge',
            'Payment Mode',
            'Payment Reference',
        ];
    }

    public function map($invoice): array
    {
        return [
            $invoice->invoice_number,
            optional($invoice->issued_at)->format('d-m-Y'),
            $invoice->financial_year,
            $invoice->buyer_name,
            $invoice->buyer_company ?: '',
            $invoice->buyer_gstin ?: 'Unregistered',
            $invoice->isIntraState() ? 'Intra-State' : 'Inter-State',
            $invoice->place_of_supply ?: '',
            $invoice->place_of_supply_code ?: '',
            $invoice->sac_code ?: '',
            number_format((float) $invoice->taxable_value, 2, '.', ''),
            (float) $invoice->cgst_rate,
            number_format((float) $invoice->cgst_amount, 2, '.', ''),
            (float) $invoice->sgst_rate,
            number_format((float) $invoice->sgst_amount, 2, '.', ''),
            (float) $invoice->igst_rate,
            number_format((float) $invoice->igst_amount, 2, '.', ''),
            number_format((float) $invoice->total_tax, 2, '.', ''),
            number_format((float) $invoice->total_amount, 2, '.', ''),
            $invoice->reverse_charge ? 'Y' : 'N',
            $invoice->payment_gateway ?: '',
            $invoice->payment_reference ?: '',
        ];
    }
}
