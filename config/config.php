<?php

declare(strict_types=1);

return [
    'app_env' => $_ENV['APP_ENV'],
    'server'    => [
        'mode' => SWOOLE_PROCESS,
        'http' => [
            'ip' => '0.0.0.0',
            'port' => 39001,
            'sock_type' => SWOOLE_SOCK_TCP,
            'callbacks' => [],
            'settings' => [
                'worker_num' => swoole_cpu_num(),
                'log_level' => SWOOLE_LOG_INFO,
                'pid_file' => RUNTIME_ROOT . '/server/http_server.pid',
                'stats_file' => RUNTIME_ROOT . '/server/http_server.stats'
            ],
        ],
    ],
    'database'    => [
        'adapter'  => $_ENV['DB_ADAPTER'],
        'host'     => $_ENV['DB_HOST'],
        'port'     => $_ENV['DB_PORT'],
        'username' => $_ENV['DB_USERNAME'],
        'password' => $_ENV['DB_PASSWORD'],
        'dbname'   => $_ENV['DB_NAME'],
    ],
    'logger'      => [
        'path'     => RUNTIME_ROOT . '/logs',
        'format'   => '[%date%][%level%] %message%',
        'date'     => 'Y-m-d H:i:s',
        'logLevel' => $_ENV['LOG_LEVEL'],
        'filename' => 'main.log',
    ],
    'cache' => [
        'adapter' => 'redis', //memory, redis, stream
        'defaultSerializer' => 'Php', //Base64, Igbinary, Json, Msgpack, None, Php
        'lifetime'          => 7200,
        'prefix'          => 'foodbase',
        'redis' => [
            'host' => $_ENV['REDIS_HOST'],
            'port' => intval($_ENV['REDIS_PORT']),
            'auth' => $_ENV['REDIS_AUTH'],
            'index' => 1,
            'persistent' => false
        ],
        'stream' => [
            'storageDir' => RUNTIME_ROOT . '/cache'
        ]
    ],
    'application' => [
        'sessionSavePath' => RUNTIME_ROOT . '/cache/session',
    ],
    'redis_pool' => [
        'host' => $_ENV['REDIS_HOST'],
        'port' => intval($_ENV['REDIS_PORT']),
        'auth' => $_ENV['REDIS_AUTH'],
        'db_index' => 0,
        'time_out' => 1,
        'size' => 64,
    ],
    'allowedFiles' => [
        'css' => 'text/css',
        'js' => 'text/javascript',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'jpg' => 'image/jpg',
        'jpeg' => 'image/jpg'
    ]
];
