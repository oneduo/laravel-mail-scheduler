<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Max attempts
    |--------------------------------------------------------------------------
    |
    | Can be used to define the maximum number of attempts to send an email.
    | Each time an email fails to send, the number of attempts is incremented.
    |
    | default: 3
    */
    'max_attempts' => (int) env('MAIL_SCHEDULER_MAX_ATTEMPTS', 3),

    /*
    |--------------------------------------------------------------------------
    | Batch size
    |--------------------------------------------------------------------------
    |
    | Can be used to define the number of scheduled emails to send in a batch.
    |
    | default: 100
    */
    'batch_size' => (int) env('MAIL_SCHEDULER_BATCH_SIZE', 100),

    /*
    |--------------------------------------------------------------------------
    | Auto schedule
    |--------------------------------------------------------------------------
    |
    | By setting this option to true, the package will auto register into the
    | Laravel scheduler.
    |
    | default: true
    */
    'auto_schedule' => (bool) env('MAIL_SCHEDULER_AUTO_SCHEDULE', true),

    /*
    |--------------------------------------------------------------------------
    | Auto schedule CRON expression
    |--------------------------------------------------------------------------
    |
    | If auto schedule is enabled, this CRON expression will be used to schedule
    | the command to run.
    |
    | default: every five minutes
    */
    'schedule_cron' => env('MAIL_SCHEDULER_SCHEDULE_CRON', '*/5 * * * *'),

    /*
    |--------------------------------------------------------------------------
    | Table name
    |--------------------------------------------------------------------------
    |
    | Can be used to define a custom table name for the scheduled emails models.
    |
    | default: scheduled_emails
    */
    'table_name' => env('MAIL_SCHEDULER_TABLE_NAME', 'scheduled_emails'),

    /*
    |--------------------------------------------------------------------------
    | Insert chunk size
    |--------------------------------------------------------------------------
    |
    | Can be used to define the size of insert chunk when using the createMany
    | method.
    |
    | default: 500
    */
    'insert_chunk_size' => (int) env('MAIL_SCHEDULER_INSERT_CHUNK_SIZE', 500),

];
