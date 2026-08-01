<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Dealer;
use App\Models\Invoice;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InvoiceService
{
    /**
     * Issue (or return the existing) GST tax invoice for a wallet recharge.
     *
     * $party is the Dealer or Customer being invoiced. $baseAmount is the
     * taxable value BEFORE GST - i.e. the amount credited to the wallet.
     */
    public function issueForRecharge(
        Dealer|Customer $party,
        float $baseAmount,
        ?int $transactionId = null,
        ?string $gateway = null,
        ?string $paymentReference = null
    ): ?Invoice {
        $partyType = $party instanceof Dealer ? 'dealer' : 'customer';

        try {
            return DB::transaction(function () use ($party, $partyType, $baseAmount, $transactionId, $gateway, $paymentReference) {
                // Never issue two invoices for the same transaction.
                if ($transactionId) {
                    $existing = Invoice::where('wallet_transaction_type', $partyType)
                        ->where('wallet_transaction_id', $transactionId)
                        ->lockForUpdate()
                        ->first();

                    if ($existing) {
                        return $existing;
                    }
                }

                $issuedAt = now('Asia/Kolkata');
                $financialYear = Invoice::financialYear($issuedAt);

                // Gap-free sequence per financial year. lockForUpdate serialises
                // concurrent recharges so two invoices cannot take one number.
                $lastSequence = (int) Invoice::where('financial_year', $financialYear)
                    ->lockForUpdate()
                    ->max('sequence');

                $sequence = $lastSequence + 1;

                $supplierState = Setting::getInvoiceSupplierState();
                $supplierStateCode = Invoice::stateCode($supplierState);
                $buyerState = $party->state;
                $buyerStateCode = Invoice::stateCode($buyerState);

                $taxable = round($baseAmount, 2);
                $rate = (float) Setting::getInvoiceGstRate();

                // Place of supply falls back to the supplier state when the buyer
                // has not recorded one, which keeps the invoice internally valid.
                $placeOfSupply = $buyerState ?: $supplierState;
                $placeOfSupplyCode = $buyerStateCode ?: $supplierStateCode;

                // Intra-state -> CGST + SGST (half each). Inter-state -> IGST.
                $intraState = $supplierStateCode !== null
                    && $placeOfSupplyCode !== null
                    && $supplierStateCode === $placeOfSupplyCode;

                $cgstRate = $sgstRate = $igstRate = 0.0;
                $cgstAmount = $sgstAmount = $igstAmount = 0.0;

                if ($intraState) {
                    $cgstRate = $sgstRate = round($rate / 2, 2);
                    $cgstAmount = round($taxable * $cgstRate / 100, 2);
                    $sgstAmount = round($taxable * $sgstRate / 100, 2);
                } else {
                    $igstRate = $rate;
                    $igstAmount = round($taxable * $igstRate / 100, 2);
                }

                $totalTax = round($cgstAmount + $sgstAmount + $igstAmount, 2);

                return Invoice::create([
                    'invoice_number' => $this->formatNumber($financialYear, $sequence),
                    'financial_year' => $financialYear,
                    'sequence' => $sequence,
                    'issued_at' => $issuedAt,

                    'dealer_id' => $partyType === 'dealer' ? $party->id : null,
                    'customer_id' => $partyType === 'customer' ? $party->id : null,
                    'wallet_transaction_id' => $transactionId,
                    'wallet_transaction_type' => $partyType,
                    'payment_gateway' => $gateway,
                    'payment_reference' => $paymentReference,

                    'supplier_name' => Setting::getInvoiceSupplierName(),
                    'supplier_gstin' => Setting::getInvoiceSupplierGstin(),
                    'supplier_address' => Setting::getInvoiceSupplierAddress(),
                    'supplier_state' => $supplierState,
                    'supplier_state_code' => $supplierStateCode,

                    'buyer_name' => $party->name ?: 'Customer',
                    'buyer_company' => $party->company_name,
                    'buyer_gstin' => $party->gst_number,
                    'buyer_address' => $party->address,
                    'buyer_city' => $party->city,
                    'buyer_state' => $buyerState,
                    'buyer_pincode' => $party->pincode,
                    'buyer_phone' => $party->phone,
                    'buyer_email' => $party->email,

                    'place_of_supply' => $placeOfSupply,
                    'place_of_supply_code' => $placeOfSupplyCode,
                    'sac_code' => Setting::getInvoiceSacCode(),
                    'description' => 'Wallet recharge - prepaid balance for vehicle information services',
                    'reverse_charge' => false,

                    'taxable_value' => $taxable,
                    'cgst_rate' => $cgstRate,
                    'cgst_amount' => $cgstAmount,
                    'sgst_rate' => $sgstRate,
                    'sgst_amount' => $sgstAmount,
                    'igst_rate' => $igstRate,
                    'igst_amount' => $igstAmount,
                    'total_tax' => $totalTax,
                    'total_amount' => round($taxable + $totalTax, 2),
                ]);
            });
        } catch (\Throwable $e) {
            // An invoicing failure must never break a completed payment.
            Log::error('Invoice generation failed', [
                'party' => $partyType,
                'party_id' => $party->id,
                'transaction_id' => $transactionId,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    private function formatNumber(string $financialYear, int $sequence): string
    {
        $prefix = trim(Setting::getInvoicePrefix(), '/');

        return sprintf('%s/%s/%04d', $prefix, $financialYear, $sequence);
    }
}
