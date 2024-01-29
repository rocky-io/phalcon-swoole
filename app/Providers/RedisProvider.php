<?php

declare(strict_types=1);

namespace App\Providers;

use Phalcon\Config\Config;
use Simps\DB\BaseRedis;
use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use RuntimeException;

class RedisProvider implements ServiceProviderInterface
{
    /**
     * @var string
     */
    protected $providerName = 'redis';

    /**
     * @param DiInterface $di
     *
     * @return void
     */
    public function register(DiInterface $di): void
    {
        /** @var Config $config */

        $di->set($this->providerName, function () use ($di) {
            $config = $di->getShared('config')->get('redis_pool')->toArray();
            $redis = new BaseRedis($config);
            return $redis;
        });
    }
}
