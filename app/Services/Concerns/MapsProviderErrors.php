<?php

namespace App\Services\Concerns;

/**
 * Turns third-party lookup failures into messages that are safe to show a
 * dealer or customer. Raw provider payloads stay in the logs - they leak
 * internal detail and mean nothing to the person who ran the search.
 */
trait MapsProviderErrors
{
    /**
     * @param  string  $subject  what was being looked up, e.g. "vehicle" or "challan record"
     */
    protected function providerFailureMessage(int $status, string $subject = 'record'): string
    {
        return match (true) {
            $status === 401 || $status === 403 => 'The verification service rejected our request. Please contact SAHI GADI support.',
            $status === 404 => "No records were found for this {$subject}.",
            $status === 429 => 'The verification service is busy right now. Please wait a moment and try again.',
            $status >= 500 => "This {$subject} could not be verified right now. This is usually a temporary issue at the source. Please try again in a few minutes.",
            default => "We could not complete the lookup for this {$subject}. Please try again.",
        };
    }

    /** Network-level failure: timeout, DNS, TLS. */
    protected function providerConnectionMessage(string $subject = 'record'): string
    {
        return "The verification service is not responding. Please try again shortly.";
    }

    /** Appended for dealer flows, where the wallet is only debited on success. */
    protected function noChargeNotice(): string
    {
        return ' No amount has been charged.';
    }

    /** Appended for customer flows, which deduct up front and refund on failure. */
    protected function refundNotice(): string
    {
        return ' Any amount deducted has been refunded to your wallet.';
    }
}
