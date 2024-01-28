<?php
declare(strict_types=1);

use App\Providers\ConfigProvider;
use App\Providers\DbProvider;
use App\Providers\DispatcherProvider;
use App\Providers\LoggerProvider;
use App\Providers\RouterProvider;
use App\Providers\SessionBagProvider;
use App\Providers\SessionProvider;
use App\Providers\CacheProvider;
use App\Providers\RedisProvider;

return [
    ConfigProvider::class,
    DbProvider::class,
    DispatcherProvider::class,
    LoggerProvider::class,
    RouterProvider::class,
    SessionBagProvider::class,
    SessionProvider::class,
    CacheProvider::class,
    RedisProvider::class
];
