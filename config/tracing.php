<?php

return [
    'default' => env('TRACING_DEFAULT', 'null'),

    'capture_in_queue' => env('TRACING_CAPTURE_IN_QUEUE', false),

    'queue_connection' => env('TRACING_QUEUE_CONNECTION'),

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
