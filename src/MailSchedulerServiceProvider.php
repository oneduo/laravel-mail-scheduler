<?php

declare(strict_types=1);

namespace Oneduo\MailScheduler;

use Illuminate\Console\Scheduling\Schedule;
use Oneduo\MailScheduler\Console\Commands\SendEmails;
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
            ->hasCommands(SendEmails::class)
            ->runsMigrations();
    }

    public function boot()
    {
        parent::boot();

        if (config('mail-scheduler.auto_schedule') === true) {
            $schedule = app(Schedule::class);

            $schedule->command(SendEmails::class)
                ->cron(config('mail-scheduler.schedule_cron'));
        }
    }
}
