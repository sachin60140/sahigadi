<?php

namespace Tests\Feature\Security;

use App\Casts\EncryptedIdentity;
use App\Models\Customer;
use App\Models\Dealer;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Aadhaar and PAN have to be collected, so the question is how they are stored.
 * They used to sit in the database in plaintext behind nothing but $hidden -
 * which keeps them out of JSON but does nothing if the database itself is read:
 * a stolen backup, an injection, a shared database host.
 *
 * They are encrypted at rest now. The cast deliberately tolerates plaintext on
 * read, so the columns could be converted without every existing row breaking
 * the moment the cast went live.
 */
class IdentityEncryptionTest extends TestCase
{
    use DatabaseTransactions;

    public function test_a_customer_identity_number_never_reaches_the_column_in_the_clear(): void
    {
        $aadhaar = '123456789012';
        $pan = 'ABCDE1234F';

        $customer = Customer::create([
            'phone' => '9500000001',
            'aadhaar_number' => $aadhaar,
            'pan_number' => $pan,
        ]);

        $row = DB::table('customers')->where('id', $customer->id)->first();

        $this->assertNotSame($aadhaar, $row->aadhaar_number, 'Aadhaar was stored in the clear.');
        $this->assertNotSame($pan, $row->pan_number, 'PAN was stored in the clear.');
        $this->assertStringNotContainsString($aadhaar, $row->aadhaar_number);
        $this->assertStringNotContainsString($pan, $row->pan_number);

        // ...and still reads back correctly.
        $fresh = Customer::find($customer->id);
        $this->assertSame($aadhaar, $fresh->aadhaar_number);
        $this->assertSame($pan, $fresh->pan_number);
    }

    public function test_a_dealer_pan_and_kyc_number_are_encrypted(): void
    {
        // PAN is required at dealer registration, so every dealer has one.
        $dealer = Dealer::first();

        if (! $dealer) {
            $this->markTestSkipped('No dealer to exercise.');
        }

        $dealer->pan_number = 'ZYXWV9876E';
        $dealer->kyc_document_number = 'KYC-00099';
        $dealer->save();

        $row = DB::table('dealers')->where('id', $dealer->id)->first();

        $this->assertStringNotContainsString('ZYXWV9876E', $row->pan_number);
        $this->assertStringNotContainsString('KYC-00099', $row->kyc_document_number);

        $fresh = Dealer::find($dealer->id);
        $this->assertSame('ZYXWV9876E', $fresh->pan_number);
        $this->assertSame('KYC-00099', $fresh->kyc_document_number);
    }

    public function test_a_value_still_in_plaintext_is_read_back_unchanged(): void
    {
        // The transition case. Laravel's built-in `encrypted` cast throws on a
        // value that is not ciphertext, which would have meant every existing
        // row breaking the instant the cast shipped.
        $customer = Customer::create(['phone' => '9500000002']);

        DB::table('customers')->where('id', $customer->id)->update(['pan_number' => 'PLAIN1234X']);

        $this->assertSame('PLAIN1234X', Customer::find($customer->id)->pan_number);
    }

    public function test_the_backfill_converts_plaintext_and_can_be_run_twice(): void
    {
        $customer = Customer::create(['phone' => '9500000003']);
        DB::table('customers')->where('id', $customer->id)->update(['aadhaar_number' => '999988887777']);

        $this->artisan('identity:encrypt-existing')->assertSuccessful();

        $afterFirst = DB::table('customers')->where('id', $customer->id)->value('aadhaar_number');

        $this->assertNotSame('999988887777', $afterFirst);
        $this->assertSame('999988887777', Customer::find($customer->id)->aadhaar_number);

        // Running it again must not encrypt the ciphertext a second time.
        $this->artisan('identity:encrypt-existing')->assertSuccessful();

        $afterSecond = DB::table('customers')->where('id', $customer->id)->value('aadhaar_number');

        $this->assertSame($afterFirst, $afterSecond, 'The value was encrypted twice.');
        $this->assertSame('999988887777', Customer::find($customer->id)->aadhaar_number);
    }

    public function test_a_dry_run_changes_nothing(): void
    {
        $customer = Customer::create(['phone' => '9500000004']);
        DB::table('customers')->where('id', $customer->id)->update(['pan_number' => 'DRYRUN123X']);

        $this->artisan('identity:encrypt-existing', ['--dry-run' => true])->assertSuccessful();

        $this->assertSame(
            'DRYRUN123X',
            DB::table('customers')->where('id', $customer->id)->value('pan_number'),
            'A dry run wrote to the database.'
        );
    }

    public function test_identity_numbers_are_still_kept_out_of_json(): void
    {
        // Encryption is in addition to $hidden, not instead of it: /api/auth/user
        // returns the Customer model straight to the mobile app.
        $customer = Customer::create([
            'phone' => '9500000005',
            'aadhaar_number' => '111122223333',
            'pan_number' => 'QWERT5678Z',
        ]);

        $json = $customer->toArray();

        $this->assertArrayNotHasKey('aadhaar_number', $json);
        $this->assertArrayNotHasKey('pan_number', $json);
    }

    public function test_the_encryption_probe_tells_ciphertext_from_plaintext(): void
    {
        $this->assertFalse(EncryptedIdentity::isEncrypted('123456789012'));
        $this->assertFalse(EncryptedIdentity::isEncrypted(null));
        $this->assertFalse(EncryptedIdentity::isEncrypted(''));
        $this->assertTrue(EncryptedIdentity::isEncrypted(Crypt::encryptString('123456789012')));
    }

    public function test_the_columns_are_wide_enough_for_ciphertext(): void
    {
        // A 10-character PAN encrypts to about 200 characters, so varchar(255)
        // had almost no margin and would have truncated into something that
        // could never be decrypted.
        $this->assertGreaterThan(
            255,
            strlen(Crypt::encryptString('123456789012')) + 100,
            'Sanity check on the envelope size assumption.'
        );

        foreach ([['customers', 'aadhaar_number'], ['customers', 'pan_number'], ['dealers', 'pan_number'], ['dealers', 'kyc_document_number']] as [$table, $column]) {
            $type = DB::selectOne("SHOW COLUMNS FROM `$table` WHERE Field = ?", [$column])->Type ?? '';

            $this->assertStringContainsString('text', strtolower($type), "$table.$column is still $type");
        }
    }
}
