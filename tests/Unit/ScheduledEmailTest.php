<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Oneduo\MailScheduler\Enums\EmailStatus;
use Oneduo\MailScheduler\Exceptions\NotAMailable;
use Oneduo\MailScheduler\Models\ScheduledEmail;
use Oneduo\MailScheduler\Tests\Support\TestEncryptedMailable;
use Oneduo\MailScheduler\Tests\Support\TestModel;
use function Pest\Laravel\assertDatabaseHas;

it('should create a scheduled email instance for a mailable', function () {
    $mail = mailable();

    $recipients = recipients();

    ScheduledEmail::fromMailable($mail, $recipients);

    assertDatabaseHas(config('mail-scheduler.table_name'), [
        'mailable' => serialize($mail),
    ]);
});

it('should create a scheduled email instance for a mailable with a source', function () {
    $mail = mailable();

    $recipients = recipients();

    Schema::create('test_models', function (Blueprint $table) {
        $table->id();
        $table->timestamps();
    });

    $source = TestModel::query()->create();

    $scheduledEmail = ScheduledEmail::fromMailable($mail, $recipients, $source);

    assertDatabaseHas(config('mail-scheduler.table_name'), [
        'source_id' => $source->getKey(),
        'source_type' => $source->getMorphClass(),
    ]);

    expect($scheduledEmail->source)->toBeInstanceOf(TestModel::class);
});

it('should throw an error when creating a scheduled email instance for a non mailable', function () {
    $mail = new Reflection();

    $recipients = recipients();

    ScheduledEmail::query()->create([
        'recipients' => $recipients,
        'mailable' => $mail,
        'status' => EmailStatus::PENDING,
    ]);
})->throws(NotAMailable::class);

it('should throw an error when a scheduled email mailable is invalid', function () {
    $mail = new Reflection();

    $recipients = recipients();

    ScheduledEmail::query()->insert([
        'id' => 1,
        'recipients' => serialize($recipients),
        'mailable' => serialize($mail),
        'status' => EmailStatus::PENDING->value,
    ]);

    $scheduledEmail = ScheduledEmail::query()->find(1);

    $scheduledEmail->mailable;
})->throws(NotAMailable::class);

it('json serializes scheduled emails with attribute', function () {
    $instance = ScheduledEmail::serializeWithAttributes([
        'mailable' => mailable(),
        'recipients' => recipients(),
    ]);

    expect(json_encode($instance))->toBeJson();
});

it('should create a scheduled email instance for an encrypted mailable', function () {
    $mail = encryptedMailable();

    $recipients = recipients();

    $mail = ScheduledEmail::fromMailable($mail, $recipients);

    $mailable = $mail->getRawOriginal('mailable');

    expect(unserialize(decrypt($mailable)))->toBeInstanceOf(TestEncryptedMailable::class);
});

it('it casts mailable when it implements encryption', function () {
    $mail = encryptedMailable();

    $recipients = recipients();

    $mail = ScheduledEmail::fromMailable($mail, $recipients);

    expect($mail->mailable)->toBeInstanceOf(TestEncryptedMailable::class);
});
