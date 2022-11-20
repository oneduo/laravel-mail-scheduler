<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\LazyCollection;
use Oneduo\MailScheduler\Console\Commands\SendEmails;
use Oneduo\MailScheduler\Enums\EmailStatus;
use Oneduo\MailScheduler\Models\ScheduledEmail as ScheduledEmailModel;
use Oneduo\MailScheduler\Support\Facades\ScheduledEmail;
use Oneduo\MailScheduler\Tests\Support\TestMailable;
use Symfony\Component\Console\Command\Command;
use function Pest\Laravel\artisan;
use function Pest\Laravel\assertDatabaseCount;

it('runs the command and sends the scheduled emails', function () {
    Mail::fake();

    collect(range(1, 10))
        ->each(fn() => ScheduledEmail::make(mailable: mailable(), recipients: recipients())->save());

    artisan(SendEmails::class)->assertExitCode(Command::SUCCESS);

    Mail::assertSent(TestMailable::class);
});

it('fails to send scheduled emails without recipients', function () {
    Mail::fake();

    collect(range(1, 10))
        ->each(function () {
            ScheduledEmailModel::query()->create([
                'mailable' => mailable(),
                'recipients' => [],
                'status' => EmailStatus::PENDING,
            ]);
        });

    artisan(SendEmails::class)->assertExitCode(Command::SUCCESS);

    $statuses = ScheduledEmailModel::query()->pluck('status');

    expect($statuses->filter(fn(EmailStatus $status) => $status !== EmailStatus::ERROR))->toBeEmpty();
});

it('fails to send emails that exceeded max attempts', function () {
    Mail::fake();

    ScheduledEmailModel::query()->create([
        'mailable' => mailable(),
        'recipients' => recipients(),
        'attempts' => config('mail-scheduler.max_attempts'),
        'status' => EmailStatus::PENDING,
    ]);

    artisan(SendEmails::class)->assertExitCode(Command::SUCCESS);

    Mail::assertNothingSent();
});

it('should not send emails which are already sent', function () {
    Mail::fake();

    ScheduledEmailModel::query()->create([
        'mailable' => mailable(),
        'recipients' => recipients(),
        'status' => EmailStatus::SENT,
    ]);

    artisan(SendEmails::class)->assertExitCode(Command::SUCCESS);

    Mail::assertNothingSent();
});

it('it can create many mails from a collection', function () {
    $max = mt_rand(400, 1000);

    $collection = collect(range(1, $max))
        ->map(function () {
            return ScheduledEmail::mailable(mailable())
                ->to(recipients())
                ->model();
        });

    ScheduledEmail::createMany($collection);

    assertDatabaseCount(config('mail-scheduler.table_name'), $max);
});

it('it can create many mails from a lazy collection', function () {
    $max = mt_rand(400, 1000);

    $collection = LazyCollection::make(function () use ($max) {
        foreach (range(1, $max) as $index) {
            yield ScheduledEmail::mailable(mailable())
                ->to(recipients())
                ->model();
        }
    });


    ScheduledEmail::createMany($collection);

    assertDatabaseCount(config('mail-scheduler.table_name'), $max);
});
