<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Oneduo\MailScheduler\Enums\EmailStatus;
use Oneduo\MailScheduler\Exceptions\MailableException;
use Oneduo\MailScheduler\Exceptions\RecipientException;
use Oneduo\MailScheduler\Models\ScheduledEmail as ScheduledEmailModel;
use Oneduo\MailScheduler\Support\Facades\ScheduledEmail;
use Oneduo\MailScheduler\Tests\Support\TestEncryptedMailable;
use Oneduo\MailScheduler\Tests\Support\TestModel;
use function Pest\Laravel\assertDatabaseHas;

it('should create a scheduled email instance for a mailable', function () {
    $mailable = mailable();

    $recipients = recipients();

    ScheduledEmail::make(mailable: $mailable, recipients: $recipients)->save();

    assertDatabaseHas(config('mail-scheduler.table_name'), [
        'mailable' => serialize($mailable),
    ]);
});

it('should create a scheduled email instance for a mailable with a source', function () {
    $mailable = mailable();

    $recipients = recipients();

    Schema::create('test_models', function (Blueprint $table) {
        $table->id();
        $table->timestamps();
    });

    $source = TestModel::query()->create();

    $scheduledEmail = ScheduledEmail::make(mailable: $mailable, recipients: $recipients, source: $source)->save();

    assertDatabaseHas(config('mail-scheduler.table_name'), [
        'source_id' => $source->getKey(),
        'source_type' => $source->getMorphClass(),
    ]);

    expect($scheduledEmail->source)->toBeInstanceOf(TestModel::class);
});

it('should throw an error when creating a scheduled email instance for a non mailable', function () {
    $mailable = new Reflection();

    $recipients = recipients();

    ScheduledEmailModel::query()->create([
        'recipients' => $recipients,
        'mailable' => $mailable,
        'status' => EmailStatus::PENDING,
    ]);
})->throws(MailableException::class);

it('should throw an error when a scheduled email mailable is invalid', function () {
    $mailable = new Reflection();

    $recipients = recipients();

    ScheduledEmailModel::query()->insert([
        'id' => 1,
        'recipients' => serialize($recipients),
        'mailable' => serialize($mailable),
        'status' => EmailStatus::PENDING->value,
    ]);

    /** @var \Oneduo\MailScheduler\Models\ScheduledEmail $scheduledEmail */
    $scheduledEmail = ScheduledEmailModel::query()->find(1);

    $scheduledEmail->mailable;
})->throws(MailableException::class);

it('json serializes scheduled emails with attribute', function () {
    $instance = ScheduledEmailModel::serializeWithAttributes([
        'mailable' => mailable(),
        'recipients' => recipients(),
    ]);

    expect(json_encode($instance))->toBeJson();
});

it('should create a scheduled email instance for an encrypted mailable', function () {
    $mailable = encryptedMailable();

    $recipients = recipients();

    $mail = ScheduledEmail::make(mailable: $mailable, recipients: $recipients, encrypted: true)->save();

    $mailable = $mail->getRawOriginal('mailable');

    expect(unserialize(decrypt($mailable)))->toBeInstanceOf(TestEncryptedMailable::class);
});

it('it casts mailable when it implements encryption', function () {
    $mailable = encryptedMailable();

    $recipients = recipients();

    $mail = ScheduledEmail::make(mailable: $mailable, recipients: $recipients, encrypted: true)->save();

    expect($mail->mailable)->toBeInstanceOf(TestEncryptedMailable::class);
});

it('it throws an exception when mailable method is not called before save', function () {
    $recipients = recipients();

    ScheduledEmail::to($recipients)->save();
})->throws(MailableException::class, 'Mailable is required to save the ScheduledEmail instance');

it('it throws an exception when to method is not called before save', function () {
    $mailable = mailable();

    ScheduledEmail::mailable($mailable)->save();
})->throws(RecipientException::class, 'Recipients list is empty');
