<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Makes room for ciphertext in the identity columns.
 *
 * Laravel's encrypted envelope is JSON holding an iv, the payload and a MAC,
 * base64 encoded - a 12 digit Aadhaar comes out around 200 characters, which is
 * uncomfortably close to varchar(255) and would silently truncate into
 * unrecoverable data if the format ever grew. TEXT removes the question.
 *
 * Nothing queries these columns by value, so widening them costs nothing. This
 * migration only changes the type: the values are converted separately by
 * `php artisan identity:encrypt-existing`, which is safe to run at any point
 * because App\Casts\EncryptedIdentity reads plaintext and ciphertext alike.
 */
return new class extends Migration
{
    private array $columns = [
        'customers' => ['aadhaar_number', 'pan_number'],
        'dealers' => ['pan_number', 'kyc_document_number'],
    ];

    public function up(): void
    {
        foreach ($this->columns as $table => $columns) {
            foreach ($columns as $column) {
                if (Schema::hasColumn($table, $column)) {
                    Schema::table($table, function (Blueprint $blueprint) use ($column) {
                        $blueprint->text($column)->nullable()->change();
                    });
                }
            }
        }
    }

    public function down(): void
    {
        // Deliberately not narrowing back to varchar(255): any row encrypted in
        // the meantime would be truncated to something that can never be
        // decrypted. Decrypt first, then narrow by hand if it is ever wanted.
    }
};
