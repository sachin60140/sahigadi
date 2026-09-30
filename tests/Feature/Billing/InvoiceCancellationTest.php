<?php

namespace Tests\Feature\Billing;

use App\Models\Dealer;
use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * A tax invoice is cancelled, never deleted.
 *
 * Deleting the row would free its number for reuse, and Rule 46 requires a
 * consecutive, gap-free series per financial year. These tests pin that down,
 * because the failure mode is silent: nothing errors, the series is simply
 * wrong afterwards.
 */
class InvoiceCancellationTest extends TestCase
{
    use DatabaseTransactions;

    private function issue(float $amount, string $ref): Invoice
    {
        return app(InvoiceService::class)->issueForRecharge(
            Dealer::query()->firstOrFail()->fresh(),
            $amount,
            null,
            'Test',
            $ref,
        );
    }

    public function test_cancelling_records_the_reason_and_is_not_repeatable(): void
    {
        $invoice = $this->issue(1000, 'CANCEL_1');

        $this->assertTrue($invoice->cancel('Payment reversed', 1));
        $invoice->refresh();

        $this->assertTrue($invoice->isCancelled());
        $this->assertSame('Payment reversed', $invoice->cancellation_reason);
        $this->assertNotNull($invoice->cancelled_at);

        $this->assertFalse($invoice->cancel('again'), 'a cancelled invoice cannot be cancelled twice');
    }

    public function test_a_cancelled_number_is_never_reused(): void
    {
        $first = $this->issue(1000, 'CANCEL_2');
        $first->cancel('Reversed', 1);

        $next = $this->issue(500, 'CANCEL_3');

        $this->assertNotSame($first->sequence, $next->sequence);
        $this->assertGreaterThan($first->sequence, $next->sequence);
    }

    public function test_the_series_stays_gap_free_across_a_cancellation(): void
    {
        $a = $this->issue(100, 'GAP_1');
        $b = $this->issue(200, 'GAP_2');
        $b->cancel('Reversed', 1);
        $c = $this->issue(300, 'GAP_3');

        $sequences = Invoice::where('financial_year', $a->financial_year)
            ->orderBy('sequence')
            ->pluck('sequence')
            ->all();

        for ($i = 1; $i < count($sequences); $i++) {
            $this->assertSame(
                $sequences[$i - 1] + 1,
                $sequences[$i],
                'the invoice series must have no gaps'
            );
        }

        $this->assertContains($b->sequence, $sequences, 'a cancelled invoice stays in the series');
        $this->assertNotNull($c->id);
    }

    public function test_a_cancelled_invoice_is_excluded_from_revenue_but_still_listed(): void
    {
        $keep = $this->issue(1000, 'REV_1');
        $drop = $this->issue(2000, 'REV_2');
        $drop->cancel('Reversed', 1);

        $issuedIds = Invoice::issued()->pluck('id')->all();
        $cancelledIds = Invoice::cancelled()->pluck('id')->all();

        $this->assertContains($keep->id, $issuedIds);
        $this->assertNotContains($drop->id, $issuedIds, 'cancelled invoices are not revenue');
        $this->assertContains($drop->id, $cancelledIds, 'but they remain on the register');
    }
}
