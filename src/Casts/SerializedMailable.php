<?php

declare(strict_types=1);

namespace Oneduo\MailScheduler\Casts;

use ErrorException;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Oneduo\MailScheduler\Exceptions\NotAMailable;

class SerializedMailable implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return ?\Illuminate\Contracts\Mail\Mailable
     *
     * @throws \Oneduo\MailScheduler\Exceptions\NotAMailable
     */
    public function get($model, string $key, $value, array $attributes): ?Mailable
    {
        try {
            $value = unserialize($value);
        } catch (ErrorException $e) {
            $value = unserialize(decrypt($value));
        }

        if (!$value instanceof Mailable) {
            throw NotAMailable::instanceOf($value::class);
        }

        return $value;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
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

        return tap(serialize($value), function (&$serialized) use ($value) {
            if ($value instanceof ShouldBeEncrypted) {
                $serialized = encrypt($serialized);
            }
        });
    }
}
