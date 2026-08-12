<?php

namespace App\Exceptions;

use Exception;

/**
 * A lookup failure whose message is already safe to show an end user.
 * Anything not of this type must be reported with a generic message, so
 * internal details (SQL, cURL, provider payloads) never reach the UI.
 */
class ProviderLookupException extends Exception
{
}
