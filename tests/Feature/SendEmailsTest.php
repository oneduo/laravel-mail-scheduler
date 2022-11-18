<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Mail;
use Oneduo\MailScheduler\Console\Commands\SendEmails;
use Oneduo\MailScheduler\Enums\EmailStatus;
use Oneduo\MailScheduler\Models\ScheduledEmail;
use Oneduo\MailScheduler\Tests\Support\TestMailable;
use function Pest\Laravel\artisan;

it('runs the command and sends the scheduled emails', function () {
    Mail::fake();

    collect(range(1, 10))
        ->each(fn() => ScheduledEmail::fromMailable(mailable(), recipients()));

    artisan(SendEmails::class)->assertOk();

    Mail::assertSent(TestMailable::class);
});

it('fails to send scheduled emails without recipients', function () {
    Mail::fake();

    collect(range(1, 10))
        ->each(fn() => ScheduledEmail::fromMailable(mailable(), []));

    artisan(SendEmails::class)->assertOk();

    $statuses = ScheduledEmail::query()->pluck('status');

    expect($statuses->filter(fn(EmailStatus $status) => $status !== EmailStatus::ERROR))->toBeEmpty();
});
