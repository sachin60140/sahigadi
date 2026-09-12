<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Raised when a wallet cannot cover a debit.
 *
 * Extends RuntimeException so existing `catch (\Exception $e)` blocks around
 * wallet operations keep working, while callers that care can catch this
 * specific type instead of matching on the message text.
 */
class InsufficientBalanceException extends RuntimeException
{
}
