<?php

namespace App\Http\Controllers\Admin;

use App\Exports\InvoicesExport;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $filters = $this->filters($request);
        $invoices = $this->applyFilters(Invoice::query(), $request)
            ->orderByDesc('issued_at')
            ->orderByDesc('sequence')
            ->paginate(25)
            ->withQueryString();

        // Totals for the current filter, not just the visible page.
        $totals = $this->applyFilters(Invoice::query(), $request)
            ->selectRaw('COUNT(*) as count, COALESCE(SUM(taxable_value),0) as taxable, COALESCE(SUM(cgst_amount),0) as cgst, COALESCE(SUM(sgst_amount),0) as sgst, COALESCE(SUM(igst_amount),0) as igst, COALESCE(SUM(total_amount),0) as total')
            ->first();

        return Inertia::render('Admin/Invoices/Index', [
            'invoices' => $invoices->through(fn (Invoice $invoice) => [
                'id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'issued_at' => optional($invoice->issued_at)->format('d M Y'),
                'buyer' => $invoice->buyer_company ?: $invoice->buyer_name,
                'buyer_gstin' => $invoice->buyer_gstin,
                'party_type' => $invoice->dealer_id ? 'Dealer' : 'Customer',
                'place_of_supply' => $invoice->place_of_supply,
                'is_intra_state' => $invoice->isIntraState(),
                'taxable_value' => (float) $invoice->taxable_value,
                'cgst' => (float) $invoice->cgst_amount,
                'sgst' => (float) $invoice->sgst_amount,
                'igst' => (float) $invoice->igst_amount,
                'total_amount' => (float) $invoice->total_amount,
                'download' => route('admin.invoices.download', $invoice->id),
            ]),
            'filters' => $filters,
            'financialYears' => Invoice::query()->distinct()->orderByDesc('financial_year')->pluck('financial_year')->values(),
            'totals' => [
                'count' => (int) ($totals->count ?? 0),
                'taxable' => (float) ($totals->taxable ?? 0),
                'cgst' => (float) ($totals->cgst ?? 0),
                'sgst' => (float) ($totals->sgst ?? 0),
                'igst' => (float) ($totals->igst ?? 0),
                'total' => (float) ($totals->total ?? 0),
            ],
            'actions' => [
                'settings' => route('admin.invoices.settings'),
                'exportExcel' => route('admin.invoices.exportExcel', array_filter($filters)),
            ],
        ]);
    }

    public function settings(Request $request)
    {
        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'invoice_prefix' => 'required|string|max:30',
                'invoice_supplier_name' => 'required|string|max:255',
                'invoice_supplier_gstin' => 'nullable|string|max:20',
                'invoice_supplier_address' => 'required|string|max:500',
                'invoice_supplier_state' => 'required|string|max:100',
                'invoice_sac_code' => 'required|string|max:20',
                'invoice_gst_rate' => 'required|numeric|min:0|max:100',
            ]);

            Setting::setInvoiceSettings($validated);

            return redirect()->back()->with('success', 'Invoice settings updated successfully!');
        }

        $supplierState = Setting::getInvoiceSupplierState();

        return Inertia::render('Admin/Invoices/Settings', [
            'settings' => [
                'invoice_prefix' => Setting::getInvoicePrefix(),
                'invoice_supplier_name' => Setting::getInvoiceSupplierName(),
                'invoice_supplier_gstin' => Setting::getInvoiceSupplierGstin(),
                'invoice_supplier_address' => Setting::getInvoiceSupplierAddress(),
                'invoice_supplier_state' => $supplierState,
                'invoice_sac_code' => Setting::getInvoiceSacCode(),
                'invoice_gst_rate' => Setting::getInvoiceGstRate(),
            ],
            'meta' => [
                'supplier_state_code' => Invoice::stateCode($supplierState),
                'next_number' => $this->previewNextNumber(),
                'states' => collect(array_unique(array_values(Invoice::STATE_CODES)))->sort()->values(),
            ],
            'actions' => [
                'update' => route('admin.invoices.settings'),
                'register' => route('admin.invoices.index'),
            ],
        ]);
    }

    public function download(Invoice $invoice)
    {
        return Pdf::loadView('pdf.tax-invoice', compact('invoice'))
            ->setPaper('A4', 'portrait')
            ->download('Tax-Invoice-'.str_replace('/', '-', $invoice->invoice_number).'.pdf');
    }

    public function exportExcel(Request $request)
    {
        $invoices = $this->applyFilters(Invoice::query(), $request)
            ->orderBy('financial_year')
            ->orderBy('sequence')
            ->get();

        return Excel::download(new InvoicesExport($invoices), 'invoice-register-'.now()->format('Y-m-d').'.xlsx');
    }

    private function filters(Request $request): array
    {
        return [
            'search' => (string) $request->query('search', ''),
            'financial_year' => (string) $request->query('financial_year', ''),
            'party_type' => (string) $request->query('party_type', ''),
            'supply_type' => (string) $request->query('supply_type', ''),
            'from_date' => (string) $request->query('from_date', ''),
            'to_date' => (string) $request->query('to_date', ''),
        ];
    }

    private function applyFilters($query, Request $request)
    {
        if ($search = trim((string) $request->query('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhere('buyer_name', 'like', "%{$search}%")
                    ->orWhere('buyer_company', 'like', "%{$search}%")
                    ->orWhere('buyer_gstin', 'like', "%{$search}%");
            });
        }

        if ($fy = $request->query('financial_year')) {
            $query->where('financial_year', $fy);
        }

        if ($partyType = $request->query('party_type')) {
            $partyType === 'dealer'
                ? $query->whereNotNull('dealer_id')
                : $query->whereNotNull('customer_id');
        }

        if ($supplyType = $request->query('supply_type')) {
            $supplyType === 'intra'
                ? $query->where('igst_amount', 0)
                : $query->where('igst_amount', '>', 0);
        }

        if ($from = $request->query('from_date')) {
            $query->whereDate('issued_at', '>=', $from);
        }

        if ($to = $request->query('to_date')) {
            $query->whereDate('issued_at', '<=', $to);
        }

        return $query;
    }

    private function previewNextNumber(): string
    {
        $financialYear = Invoice::financialYear(now('Asia/Kolkata'));
        $next = ((int) Invoice::where('financial_year', $financialYear)->max('sequence')) + 1;

        return sprintf('%s/%s/%04d', trim(Setting::getInvoicePrefix(), '/'), $financialYear, $next);
    }
}
