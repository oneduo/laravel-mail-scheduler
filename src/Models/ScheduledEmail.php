<?php

declare(strict_types=1);

namespace Oneduo\MailScheduler\Models;

use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Oneduo\MailScheduler\Casts\SerializedMailable;
use Oneduo\MailScheduler\Casts\SerializedObject;
use Oneduo\MailScheduler\Enums\EmailStatus;

/**
 * @property array $recipients
 * @property \Illuminate\Mail\Mailable $mailable
 * @property \Oneduo\MailScheduler\Enums\EmailStatus $status
 * @property int $attempts
 * @property ?string $error
 * @property ?string $stacktrace
 * @property ?\Carbon\Carbon $attempted_at
 * @property ?int $source_id
 * @property ?class-string $source_type
 * @property ?\Illuminate\Database\Eloquent\Model $source
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class ScheduledEmail extends Model
{
    protected $fillable = [
        'recipients',
        'mailable',
        'status',
        'attempts',
        'error',
        'stacktrace',
        'attempted_at',
        'source_id',
        'source_type',
    ];

    protected $casts = [
        'recipients' => SerializedObject::class,
        'mailable' => SerializedMailable::class,
        'status' => EmailStatus::class,
        'attempts' => 'int',
        'attempted_at' => 'datetime',
    ];

    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), [
            'recipients' => serialize($this->recipients),
            'mailable' => serialize($this->mailable),
        ]);
    }

    public static function serializeWithAttributes(array $attributes): array
    {
        return (new static(
            array_merge([
                'status' => EmailStatus::PENDING,
                'attempts' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ], $attributes)
        ))->jsonSerialize();
    }

    public static function fromMailable(Mailable $mailable, array $recipients, ?Model $source = null): static
    {
        return static::query()->create([
            'mailable' => $mailable,
            'recipients' => $recipients,
            'status' => EmailStatus::PENDING,
            'source_id' => $source?->getKey(),
            'source_type' => $source?->getMorphClass(),
        ]);
    }
}
