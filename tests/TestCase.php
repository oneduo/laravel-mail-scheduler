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

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        foreach (['create_scheduled_emails_table', 'add_mailer_to_scheduled_emails_table'] as $migration) {
            $migration = include __DIR__."/../database/migrations/{$migration}.php.stub";
            $migration->up();
        }
    }
}
