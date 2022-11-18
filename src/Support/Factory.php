<?php

declare(strict_types=1);

namespace Oneduo\MailScheduler\Support;

use Illuminate\Support\Traits\Macroable;

class Factory
{
    use Macroable {
        __call as macroCall;
    }

    protected function newPendingScheduledEmail(): PendingScheduledEmail
    {
        return new PendingScheduledEmail();
    }

    /**
     * Execute a method against a new pending scheduled email instance.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        return $this->newPendingScheduledEmail()->{$method}(...$parameters);
    }

}
