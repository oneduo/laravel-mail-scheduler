<?php

declare(strict_types=1);

namespace Oneduo\MailScheduler;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class MailSchedulerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-mail-scheduler')
            ->hasConfigFile()
            ->hasMigration('create_scheduled_emails_table')
            ->runsMigrations();
    }
}
