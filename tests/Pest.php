<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Mail\Mailable;
use Oneduo\MailScheduler\Tests\Support\TestMailable;
use Oneduo\MailScheduler\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);
uses(LazilyRefreshDatabase::class)->in(__DIR__);

function mailable(): Mailable
{
    return new TestMailable();
}

function recipients(): array
{
    return [
        ...collect(range(1, 10))->map(fn () => fake()->email),
    ];
}
