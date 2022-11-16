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
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return ?\Illuminate\Contracts\Mail\Mailable
     *
     * @throws \Oneduo\MailScheduler\Exceptions\NotAMailable
     */
    public function get($model, string $key, $value, array $attributes): ?Mailable
    {
        if (!filled($value)) {
            return null;
        }

        return tap(unserialize($value), function ($mailable) {
            if (!$mailable instanceof Mailable) {
                throw NotAMailable::instanceOf($mailable::class);
            }
        });
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return ?string
     *
     * @throws \Oneduo\MailScheduler\Exceptions\NotAMailable
     */
    public function set($model, string $key, $value, array $attributes): ?string
    {
        if (!filled($value)) {
            return null;
        }

        return tap($value, function ($mailable) {
            if (!$mailable instanceof Mailable) {
                throw NotAMailable::instanceOf($mailable::class);
            }

            return serialize($mailable);
        });
    }
}
