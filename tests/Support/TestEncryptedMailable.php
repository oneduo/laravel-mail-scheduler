<?php

declare(strict_types=1);

namespace Oneduo\MailScheduler\Tests\Support;

use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class TestEncryptedMailable extends Mailable implements ShouldBeEncrypted
{
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Test subject',
        );
    }

    public function content(): Content
    {
        return new Content(
            text: 'Test content',
        );
    }
}
