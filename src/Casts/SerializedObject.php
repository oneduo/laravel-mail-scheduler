<?php

declare(strict_types=1);

namespace Oneduo\MailScheduler\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class SerializedObject implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return mixed
     */
    public function get($model, string $key, $value, array $attributes): mixed
    {
        return filled($value) ? unserialize($value) : null;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return ?string
     */
    public function set($model, string $key, $value, array $attributes): ?string
    {
        return filled($value) ? serialize($value) : null;
    }
}
