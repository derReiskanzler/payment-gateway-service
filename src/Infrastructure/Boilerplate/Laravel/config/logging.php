<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "custom", "stack"
    |
    */

    'channels' => [
        'local_stdout' => [
            'driver' => 'monolog',
            'handler' => Monolog\Handler\StreamHandler::class,
            'tap' => [
                Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Logging\Processors\TraceIdLoggingProcessor::class,
            ],
            'with' => [
                'stream' => 'php://stdout',
            ],
        ],

        'local_stack' => [
            'driver' => 'stack',
            'channels' => ['single', 'local_stdout'],
        ],

        'stack' => [
            'driver' => 'stack',
            'channels' => ['single', 'kibana_log'],
        ],

        'testing' => [
            'driver' => 'stack',
            'channels' => ['single'],
        ],

        'single' => [
            'driver' => 'single',
            'tap' => [
                Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Logging\Processors\TraceIdLoggingProcessor::class,
            ],
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
            'days' => 7,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji' => ':boom:',
            'level' => 'critical',
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => 'debug',
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => 'debug',
        ],

        'dt' => [
            'driver' => 'single',
            'path' => storage_path('logs/dt.log'),
            'level' => 'debug',
        ],

        'json_stdout' => [
            'driver' => 'monolog',
            'tap' => [
                Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Logging\Processors\TraceIdLoggingProcessor::class,
            ],
            'handler' => Monolog\Handler\StreamHandler::class,
            'handler_with' => [
                'stream' => 'php://stdout',
            ],
            'formatter' => Monolog\Formatter\JsonFormatter::class,
        ],

        'kibana_log' => [
            'driver' => 'monolog',
            'tap' => [
                Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Logging\Processors\TraceIdLoggingProcessor::class,
            ],
            'handler' => Monolog\Handler\StreamHandler::class,
            'handler_with' => [
                'stream' => storage_path('logs/kibana.log'),
            ],
            'formatter' => Monolog\Formatter\JsonFormatter::class,
        ],

        'emergency' => [
            'path' => 'php://stdout',
        ],
    ],
];
