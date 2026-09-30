<?php

namespace App\Console\Commands;

use App\Models\Customer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Finishes an account deletion once the grace period has run out.
 *
 * Deletion is deliberately two-stage: DELETE /api/account hides the account
 * and revokes its tokens straight away, and this pass scrambles the personal
 * fields for good some days later. Until it runs, signing in with the same
 * number restores everything.
 */
class AnonymiseDeletedCustomers extends Command
{
    protected $signature = 'customers:anonymise-deleted
                            {--dry-run : List what would be anonymised without touching anything}';

    protected $description = 'Anonymise customers whose account deletion grace period has expired';

    public function handle(): int
    {
        $cutoff = now()->subDays(Customer::GRACE_DAYS);

        $due = Customer::onlyTrashed()
            ->whereNull('anonymised_at')
            ->where('deleted_at', '<=', $cutoff)
            ->get();

        if ($due->isEmpty()) {
            $this->info('Nothing to anonymise.');

            return self::SUCCESS;
        }

        $this->info($due->count().' account(s) past the '.Customer::GRACE_DAYS.'-day grace period.');

        foreach ($due as $customer) {
            if ($this->option('dry-run')) {
                $this->line(sprintf(
                    '  would anonymise #%d, deleted %s',
                    $customer->id,
                    $customer->deleted_at->format('d M Y')
                ));

                continue;
            }

            try {
                $customer->anonymise();

                // The id is not personal data and is what an auditor would need
                // to tie this back to a retained invoice.
                Log::info('Anonymised deleted customer', ['customer_id' => $customer->id]);

                $this->line('  anonymised #'.$customer->id);
            } catch (\Throwable $e) {
                // One failure must not stop the rest: these are legal
                // obligations with a deadline attached.
                Log::error('Failed to anonymise deleted customer', [
                    'customer_id' => $customer->id,
                    'error' => $e->getMessage(),
                ]);

                $this->error('  failed #'.$customer->id.': '.$e->getMessage());
            }
        }

        return self::SUCCESS;
    }
}
