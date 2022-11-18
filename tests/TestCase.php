<?php

declare(strict_types=1);

namespace Oneduo\MailScheduler\Tests;

use Oneduo\MailScheduler\MailSchedulerServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            MailSchedulerServiceProvider::class,
        ];
    }
}
