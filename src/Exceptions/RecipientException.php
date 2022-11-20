<?php

declare(strict_types=1);

namespace Oneduo\MailScheduler\Exceptions;

use Exception;

class RecipientException extends Exception
{
    public static function empty(): static
    {
        return new self('Recipients list is empty');
    }
}
