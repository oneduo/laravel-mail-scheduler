<?php

declare(strict_types=1);

namespace Oneduo\MailScheduler\Support\Facades;

use Illuminate\Support\Facades\Facade;
use Oneduo\MailScheduler\Support\Factory;

/**
 * @method static \Oneduo\MailScheduler\Support\PendingScheduledEmail mailable($mailable)
 * @method static \Oneduo\MailScheduler\Support\PendingScheduledEmail to($recipients)
 * @method static \Oneduo\MailScheduler\Support\PendingScheduledEmail encrypted($encrypted = true)
 * @method static \Oneduo\MailScheduler\Support\PendingScheduledEmail sendAt($send_at)
 * @method static \Oneduo\MailScheduler\Support\PendingScheduledEmail source($model)
 * @method static \Oneduo\MailScheduler\Support\PendingScheduledEmail make(\Illuminate\Mail\Mailable $mailable, array $recipients, ?\Carbon\Carbon $send_at = null, ?\Illuminate\Database\Eloquent\Model $source = null, ?bool $encrypted = false)
 * @method static void createMany(\Illuminate\Support\Enumerable $emails)
 */
class ScheduledEmail extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return Factory::class;
    }
}
