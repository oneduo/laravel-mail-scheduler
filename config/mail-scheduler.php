<?php

declare(strict_types=1);

return [

    'max_attempts' => (int) env('MAIL_SCHEDULER_MAX_ATTEMPTS', 3),

    'batch_size' => (int) env('MAIL_SCHEDULER_BATCH_SIZE', 100),

];
