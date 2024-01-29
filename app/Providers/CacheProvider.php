<?php

declare(strict_types=1);

namespace App\Providers;

use Phalcon\Config\Config;
use Phalcon\Cache\Cache;
use Phalcon\Cache\Adapter;
use Phalcon\Storage\SerializerFactory;
use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use RuntimeException;

class CacheProvider implements ServiceProviderInterface
{
    /**
     * @var string
     */
    protected $providerName = 'cache';

    /**
     * Class map of cache adapters.
     *
     * @var array
     */
    protected $adapters = [
        'memory'  => Adapter\Memory::class,
        'redis'  => Adapter\Redis::class,
        'stream'  => Adapter\Stream::class,
    ];

    /**
     * @param DiInterface $di
     *
     * @return void
     * @throws RuntimeException
     */
    public function register(DiInterface $di): void
    {
        /** @var Config $config */
        $config = $di->getShared('config')->get('cache');
        $class  = $this->getClass($config);
        $options = $this->createConfig($config);

        $serializerFactory = new SerializerFactory();
        $adapter   = new $class(
            $serializerFactory,
            $options
        );
        $cache = new Cache($adapter);
        $di->set($this->providerName, function () use ($cache) {
            return $cache;
        });
    }

    /**
     * Get an adapter class by name.
     *
     * @param Config $config
     *
     * @return string
     * @throws RuntimeException
     */
    private function getClass(Config $config): string
    {
        $name = $config->get('adapter', 'Unknown');

        if (empty($this->adapters[$name])) {
            throw new RuntimeException(
                sprintf(
                    'Adapter "%s" has not been registered',
                    $name
                )
            );
        }

        return $this->adapters[$name];
    }

    /**
     * Get options for adapter
     *
     * @param Config $config
     * @return array
     */
    private function createConfig(Config $config): array
    {
        $cacheConfig = $config->toArray();
        $adapterConfig = [
            'defaultSerializer' => $cacheConfig['defaultSerializer'],
            'lifetime'          => $cacheConfig['lifetime'],
            'prefix' =>          $cacheConfig['prefix'],
        ];
        switch ($this->adapters[$cacheConfig['adapter']]) {
            case Adapter\Memory::class:
                break;
            case Adapter\Redis::class:
                $adapterConfig = array_merge($adapterConfig, $cacheConfig['redis']);
                break;
            case Adapter\Stream::class:
                $adapterConfig['storageDir'] = $cacheConfig['stream']['storageDir'];
                break;
        }
        return $adapterConfig;
    }
}
