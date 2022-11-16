<?php

declare(strict_types=1);

namespace Oneduo\MailScheduler\Exceptions;

use Exception;

class NotAMailable extends Exception
{
    public static function instanceOf(string $class): static
    {
        return new self("[{$class}] is not a Mailable instance");
    }
}
