<?php

declare(strict_types=1);

namespace Oneduo\MailScheduler\Support;

use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Oneduo\MailScheduler\Enums\EmailStatus;
use Oneduo\MailScheduler\Models\ScheduledEmail;

class PendingScheduledEmail implements PendingScheduledEmailInt
{
    protected ScheduledEmail $scheduledEmail;

    public function __construct()
    {
        $this->scheduledEmail = new ScheduledEmail([
            'status' => EmailStatus::PENDING,
            'attempts' => 0,
        ]);
    }

    public function make(
        Mailable $mailable,
        array    $recipients,
        ?Carbon  $send_at = null,
        ?Model   $source = null,
        bool     $encrypted = false,
    ): static
    {
        return (new static())
            ->mailable($mailable)
            ->to($recipients)
            ->sendAt($send_at)
            ->source($source)
            ->encrypted($encrypted);
    }

    public function mailable(Mailable $mailable): static
    {
        $this->scheduledEmail->mailable = $mailable;

        return $this;
    }

    public function to(array $recipients): static
    {
        $this->scheduledEmail->recipients = $recipients;

        return $this;
    }

    public function encrypted(bool $encrypted = true): static
    {
        $this->scheduledEmail->encrypted = $encrypted;

        return $this;
    }

    public function sendAt(?Carbon $send_at = null): static
    {
        $this->scheduledEmail->send_at = $send_at;

        return $this;
    }

    public function source(?Model $model = null): static
    {
        $this->scheduledEmail->source_id = $model?->getKey();
        $this->scheduledEmail->source_type = $model?->getMorphClass();

        return $this;
    }

    public function save(): ScheduledEmail
    {
        $this->scheduledEmail->save();

        return $this->scheduledEmail;
    }
}