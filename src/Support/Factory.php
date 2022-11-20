<?php

declare(strict_types=1);

namespace Oneduo\MailScheduler\Support;

use Illuminate\Support\Enumerable;
use Illuminate\Support\Traits\Macroable;
use Oneduo\MailScheduler\Models\ScheduledEmail;

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
     * @param  \Illuminate\Support\Enumerable<int, \Oneduo\MailScheduler\Models\ScheduledEmail>  $emails
     *
     * @return void
     */
    public function createMany(Enumerable $emails): void
    {
        $emails
            ->chunk(config('mail-scheduler.insert_chunk_size'))
            ->each(function (Enumerable $chunk) {
                ScheduledEmail::query()->insert(
                    $chunk
                        ->map(fn (ScheduledEmail $scheduledEmail) => $scheduledEmail->getAttributes())
                        ->toArray()
                );
            });
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
