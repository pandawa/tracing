<?php

return [
    // Default tracing logger
    'default' => env('TRACING_DEFAULT', 'null'),

    // When left false the tracing will run in sync mode
    'capture_in_queue' => env('TRACING_CAPTURE_IN_QUEUE', false),

    // Determine queue connection. Config capture_in_queue should be true or string
    'queue_connection' => env('TRACING_QUEUE_CONNECTION'),

    // Trace pavana http client. Register the service key of pavana http client
    'pavana' => [],

    // Trace pavana http incoming request
    'route' => [
        'only' => [
            // Capture request only for these routes
        ],
        'excepts' => [
            // Capture request except for these routes
        ],
    ],

    // Filter value for some keys
    'filters' => [],

    // List of logger driver
    'loggers' => [
        'aliyun' => [
            'access_key_id'     => env('ALIYUN_SLS_ACCESS_KEY_ID'),
            'access_key_secret' => env('ALIYUN_SLS_ACCESS_KEY_SECRET'),
            'endpoint'          => env('ALIYUN_SLS_ENDPOINT', 'ap-southeast-5.log.aliyuncs.com'),
            'project'           => env('ALIYUN_SLS_PROJECT'),
            'log_store'         => env('ALIYUN_SLS_LOG_STORE'),
            'topic'             => env('ALIYUN_SLS_TOPIC'),
            'source'            => env('ALIYUN_SLS_SOURCE'),
        ],
    ],
];
