# Laravel Mail Scheduler

<div align="left">

![Status](https://img.shields.io/badge/status-active-success.svg)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](/LICENSE)
![PHP](https://img.shields.io/badge/PHP-8.1-blue.svg)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/oneduo/laravel-mail-scheduler.svg)](https://packagist.org/packages/oneduo/laravel-mail-scheduler)
[![Downloads](https://img.shields.io/packagist/dt/oneduo/laravel-mail-scheduler.svg)](https://packagist.org/packages/oneduo/laravel-mail-scheduler)
[![Run tests](https://github.com/oneduo/laravel-mail-scheduler/actions/workflows/tests.yml/badge.svg?branch=main)](https://github.com/oneduo/laravel-mail-scheduler/actions/workflows/tests.yml)
</div>

---

This package gives you the ability to send emails in batches. After creating ScheduledEmails you may send emails using the auto schedule feature or registering the command in the Console kernel yourself.

## Table of Contents

- [Getting Started](#getting_started)
  - [Prerequisites](#prerequisites)
  - [Installing](#installing)
  - [Configuration](#configuration)
- [Usage](#usage)
- [Configuration](#configuration-file)
- [Authors](#authors)
- [Changelog](#changelog)
- [Security](#security)
- [Contributing](#contributing)
- [Credits](#credits)
- [License](#license)

## Getting Started <a name = "getting_started"></a>

### Prerequisites

This package requires the following :

- PHP 8.1 or higher
- Laravel 8.0 or higher

### Installing

To get started, you will need to install the following dependencies :

```sh
composer require oneduo/laravel-mail-scheduler
```

That's it, you're ready to go!

### Configuration

You may publish the package's configuration by running the following command :

```sh
php artisan vendor:publish --tag="laravel-mail-scheduler-config"
```

> **Note** You can find details about the configuration options in the [configuration file section](#configuration-file).

## Usage <a name="usage"></a>

The package provides a fluent facade to create a scheduled email:

```php
<?php

use App\Mail\OrderShipped;
use Oneduo\MailScheduler\Support\Facades\ScheduledEmail;

$instance = ScheduledEmail::mailable(new OrderShipped)
    ->to(['john@doe.com'])
    ->save();
```

### Encryption

For security reasons you may want to encrypt the mailable to protect sensible data. You may use the `encrypted` method:

```php
<?php

use App\Mail\OrderShipped;
use Oneduo\MailScheduler\Support\Facades\ScheduledEmail;

$instance = ScheduledEmail::mailable(new OrderShipped)
    ->to(['john@doe.com'])
    ->encrypted() // will encrypt the mailable in database
    ->save();
```

### Link email to a source model

You may want to link a ScheduledEmail instance to one of your models using a `morphTo` relationship. It could be a user or a product. It's up to you.

```php
<?php

use App\Mail\OrderShipped;
use App\Models\Product;
use Oneduo\MailScheduler\Support\Facades\ScheduledEmail;

$product = Product::query()->first();

$instance = ScheduledEmail::mailable(new OrderShipped($product))
    ->to(['john@doe.com'])
    ->encrypted() // will encrypt the mailable in database
    ->source($product) // 
    ->save();
```

```php
<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Oneduo\MailScheduler\Models\ScheduledEmail;

class Product extends Model
{
    public function emails(): MorphMany
    {
        return $this->morphMany(ScheduledEmail::class, 'source');    
    }
}
```

### Send emails

The package can register the command for you when `auto_schedule` is true. You may configure the CRON expression with `schedule_cron`.

If you want more control on the scheduler, you may disable the `auto_schedule` and register the command yourself:

```php
<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('mail-scheduler:send')
            ->everyMinute()
            ->between('08:00', '18:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
```

### Error handling

If an exception occurs while sending an email, the exception message and stacktrace will be saved into the model. The command will resend emails with an error status till `max_attempts` is reached.

## Configuration file <a name = "configuration-file"></a>


| Key                 | Description                                                                          | Type     | Default            |
|---------------------|--------------------------------------------------------------------------------------|----------|--------------------|
| `max_attempts`      | Maximum number of attempts to send an email                                          | `int`    | `3`                |
| `batch_size`        | Number of scheduled emails to send in a batch                                        | `int`    | `100`              |
| `auto_schedule`     | Toggles whether or not to register the send email command into the Laravel scheduler | `bool`   | `true`             |
| `schedule_cron`     | The CRON expression used to send emails                                              | `string` | `*/5 * * * *`      |
| `table_name`        | The table name of the ScheduledEmail model                                           | `string` | `scheduled_emails` |
| `insert_chunk_size` | The chunk size to use to insert emails when using the `createMany` method            | `int`    | `500`              |

## Authors <a name = "authors"></a>

- [Mikaël Popowicz](https://github.com/mikaelpopowicz)
- [Charaf Rezrazi](https://github.com/rezrazi)

See also the list of [contributors](https://github.com/oneduo/nova-file-manager/contributors) who
participated in this project.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Security

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
