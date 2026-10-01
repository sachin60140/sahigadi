<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Attestr answered with a non-2xx status.
 *
 * Deliberately NOT a ProviderLookupException: that type's contract is that its
 * message is already safe to show an end user, whereas this one carries the raw
 * provider status. The calling service translates it into its own wording,
 * because the two differ - a dealer is told no amount was charged, a customer
 * is told about the refund.
 */
class AttestrRequestException extends RuntimeException
{
    public function __construct(
        public readonly int $status,
        public readonly string $body = '',
    ) {
        parent::__construct('Attestr request failed with status '.$status);
    }
}
