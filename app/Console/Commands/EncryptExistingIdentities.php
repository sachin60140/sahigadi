<?php

namespace App\Console\Commands;

use App\Casts\EncryptedIdentity;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

/**
 * Converts identity numbers already sitting in the database as plaintext.
 *
 * Safe to run repeatedly: a value that is already ciphertext is skipped, so
 * nothing is ever double-encrypted. It works on the raw columns rather than
 * through the model, because the cast would decrypt on read and re-encrypt on
 * write, which would make the pass a no-op that looked like it had worked.
 */
class EncryptExistingIdentities extends Command
{
    protected $signature = 'identity:encrypt-existing
                            {--dry-run : Report what would change without writing anything}';

    protected $description = 'Encrypt Aadhaar, PAN and KYC numbers still stored as plaintext';

    private const TARGETS = [
        'customers' => ['aadhaar_number', 'pan_number'],
        'dealers' => ['pan_number', 'kyc_document_number'],
    ];

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        if ($dryRun) {
            $this->info('Dry run: nothing will be written.');
        }

        $totalPlain = 0;
        $totalDone = 0;

        foreach (self::TARGETS as $table => $columns) {
            foreach ($columns as $column) {
                [$plain, $converted] = $this->convert($table, $column, $dryRun);
                $totalPlain += $plain;
                $totalDone += $converted;
            }
        }

        if ($totalPlain === 0) {
            $this->info('Nothing left in plaintext.');

            return self::SUCCESS;
        }

        $this->newLine();
        $this->info($dryRun
            ? "$totalPlain value(s) would be encrypted."
            : "$totalDone of $totalPlain value(s) encrypted.");

        return self::SUCCESS;
    }

    /** @return array{0:int,1:int} plaintext found, converted */
    private function convert(string $table, string $column, bool $dryRun): array
    {
        $rows = DB::table($table)
            ->select('id', $column)
            ->whereNotNull($column)
            ->where($column, '!=', '')
            ->get();

        $plain = 0;
        $converted = 0;

        foreach ($rows as $row) {
            if (EncryptedIdentity::isEncrypted($row->{$column})) {
                continue;
            }

            $plain++;

            if ($dryRun) {
                continue;
            }

            try {
                DB::table($table)
                    ->where('id', $row->id)
                    ->update([$column => Crypt::encryptString((string) $row->{$column})]);

                $converted++;
            } catch (\Throwable $e) {
                // One bad row must not stop the rest; the value is still
                // readable either way because the cast tolerates plaintext.
                $this->error("  $table#{$row->id}.$column failed: ".$e->getMessage());
            }
        }

        $encrypted = $rows->count() - $plain;
        $this->line(sprintf(
            '  %-34s %d row(s): %d already encrypted, %d plaintext',
            $table.'.'.$column,
            $rows->count(),
            $encrypted,
            $plain
        ));

        return [$plain, $converted];
    }
}
