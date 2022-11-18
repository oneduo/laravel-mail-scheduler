<?php

declare(strict_types=1);

namespace Oneduo\MailScheduler\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Mail\Mailable;
use Oneduo\MailScheduler\Exceptions\NotAMailable;

class SerializedMailable implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param \Oneduo\MailScheduler\Models\ScheduledEmail $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return ?\Illuminate\Contracts\Mail\Mailable
     *
     * @throws \Oneduo\MailScheduler\Exceptions\NotAMailable
     */
    public function get($model, string $key, $value, array $attributes): ?Mailable
    {
        $value = $model->encrypted ? unserialize(decrypt($value)) : unserialize($value);

        if (!$value instanceof Mailable) {
            throw NotAMailable::instanceOf($value::class);
        }

        return $value;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param \Oneduo\MailScheduler\Models\ScheduledEmail $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return ?string
     *
     * @throws \Oneduo\MailScheduler\Exceptions\NotAMailable
     */
    public function set($model, string $key, $value, array $attributes): ?string
    {
        if (!$value instanceof Mailable) {
            throw NotAMailable::instanceOf($value::class);
        }

        return tap(serialize($value), function (&$serialized) use ($attributes, $model, $value) {
            if (data_get($attributes, 'encrypted', false)) {
                $serialized = encrypt($serialized);
            }
        });
    }
}
