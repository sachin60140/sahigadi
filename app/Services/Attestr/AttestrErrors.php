<?php

namespace App\Services\Attestr;

/**
 * What each Attestr error code means, for the person who ran the lookup and
 * for whoever runs the site.
 *
 * The messages used to be chosen from the HTTP status alone, which hid the
 * cases that matter. Attestr returns 400 for a low credit balance, so when the
 * credit ran out every lookup on the site would fail with "Please try again"
 * and nobody would be told to top up. A daily-limit 429 was answered with "wait
 * a moment", although it does not clear until the next day. And 5001 was called
 * "usually a temporary issue", while in practice the same vehicle fails on every
 * retry.
 *
 * Codes are from the error table at
 * https://docs.attestr.com/attestr-docs/vehicle-rc-check-api. Note that a wrong
 * or unknown registration number is NOT an error: Attestr answers 200 with
 * valid=false, which the services already handle.
 */
final class AttestrErrors
{
    /**
     * Problems with OUR account or setup, which break every lookup until
     * someone acts. These alert the operator; the person searching can do
     * nothing about them.
     *
     * @var array<int, string>
     */
    public const OPERATOR = [
        4001 => 'Attestr rejected a request as malformed (4001). Something in how we call the API has changed - on our side or theirs.',
        4005 => 'The Attestr credit balance is too low (4005). Top up the Attestr wallet; every RC lookup fails until then.',
        4016 => 'Attestr rejected our credentials (4016). Check VEHICLE_API_KEY on the server.',
        4031 => 'Attestr refused access to the account (4031). Check the account status in the Attestr console.',
        4035 => 'Vehicle RC Check is not provisioned for our Attestr account (4035). This is what to expect if Attestr turns off v2 for us.',
        4039 => "The server's IP address is not whitelisted with Attestr (4039). This happens if the hosting provider moves the site.",
        4293 => "The Attestr account's daily limit has been reached (4293). Lookups resume tomorrow unless the limit is raised.",
        4294 => "The RC Check API's daily limit has been reached (4294). Lookups resume tomorrow unless the limit is raised.",
    ];

    /** Pull Attestr's own error code out of a response body. */
    public static function codeFrom(string $body): ?int
    {
        $decoded = json_decode($body, true);

        return is_array($decoded) && isset($decoded['code']) && is_numeric($decoded['code'])
            ? (int) $decoded['code']
            : null;
    }

    public static function isOperatorProblem(?int $code): bool
    {
        return $code !== null && array_key_exists($code, self::OPERATOR);
    }

    /**
     * The message for the person who ran the lookup, or null when the code
     * says nothing more than the HTTP status already does.
     */
    public static function userMessage(?int $code, string $subject = 'vehicle'): ?string
    {
        return match ($code) {
            // Attestr could not process this particular record. Confirmed on
            // live traffic: the same vehicle fails on every retry, from any
            // server, while other vehicles succeed.
            5001 => "We couldn't retrieve the record for this {$subject} from the source. This isn't caused by the number you entered, and trying again straight away is unlikely to help.",

            4293, 4294 => "Vehicle checks have reached today's limit. Please try again tomorrow.",

            4291, 4292 => 'The verification service is busy right now. Please wait a moment and try again.',

            4001, 4005, 4016, 4031, 4035, 4039 => 'Vehicle checks are temporarily unavailable. We have been notified and are looking into it. Please try again later.',

            default => null,
        };
    }
}
