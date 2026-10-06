<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Tells the operator that Attestr is refusing our requests for a reason only
 * the operator can fix - a low credit balance, rejected credentials, an
 * unwhitelisted IP, a service not provisioned, a daily limit. Every RC lookup
 * on the site fails until someone acts, so this must not wait for someone to
 * read the log.
 *
 * Carries no vehicle or personal data: the code and what to do about it are
 * all the operator needs.
 */
class AttestrAccountAlert extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public int $code,
        public string $summary,
        public string $firstSeen,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Action required: RC lookups are failing (Attestr {$this->code})",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.admin.attestr_account_alert',
        );
    }
}
