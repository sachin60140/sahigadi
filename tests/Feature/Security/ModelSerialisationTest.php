<?php

namespace Tests\Feature\Security;

use App\Models\Customer;
use App\Models\Dealer;
use Tests\TestCase;

/**
 * /api/auth/user hands the whole model to the mobile app. Identity documents
 * and internal storage paths must never ride along: the app does not use them,
 * and they end up in device memory and crash reports.
 *
 * Admin screens read these attributes directly, which $hidden does not affect.
 */
class ModelSerialisationTest extends TestCase
{
    public function test_a_customer_does_not_serialise_identity_numbers(): void
    {
        $serialised = (new Customer())->toArray();

        foreach (['aadhaar_number', 'pan_number'] as $field) {
            $this->assertArrayNotHasKey($field, $serialised);
        }
    }

    public function test_a_dealer_does_not_serialise_identity_or_document_paths(): void
    {
        $serialised = (new Dealer())->toArray();

        foreach ([
            'password',
            'remember_token',
            'pan_number',
            'kyc_document_number',
            'kyc_document_path',
            'pan_document_path',
            'gst_document_path',
        ] as $field) {
            $this->assertArrayNotHasKey($field, $serialised);
        }
    }

    public function test_hidden_fields_are_still_readable_in_code(): void
    {
        // Admin screens build their payloads by reading attributes directly.
        $dealer = new Dealer(['kyc_document_number' => 'X1234']);

        $this->assertSame('X1234', $dealer->kyc_document_number);
        $this->assertArrayNotHasKey('kyc_document_number', $dealer->toArray());
    }
}
