<?php

declare(strict_types=1);

namespace App\Providers;

use Phalcon\Config\Config;
use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Logger\Logger;
use Phalcon\Logger\Adapter\Stream;
use Phalcon\Logger\Formatter\Line;

/**
 * Logger service
 */
class LoggerProvider implements ServiceProviderInterface
{
    /**
     * @var string
     */
    protected $providerName = 'logger';

    /**
     * @param DiInterface $di
     *
     * @return void
     */
    public function register(DiInterface $di): void
    {
        /**
         *  @var Config $loggerConfigs
         */
        $loggerConfigs = $di->getShared('config')->get('logger');
        $filename = trim($loggerConfigs->get('filename'), '\\/');
        $path     = rtrim($loggerConfigs->get('path'), '\\/') . DIRECTORY_SEPARATOR;
        $formatter = new Line($loggerConfigs->get('format'), $loggerConfigs->get('date'));
        $adapter    = new Stream($path . $filename);
        $adapter->setFormatter($formatter);
        $logger = new Logger(
            'messages',
            [
                'main'   => $adapter
            ]
        );

        $logger->setLogLevel(intval($loggerConfigs->get('logLevel')));
        $di->set($this->providerName, function () use ($logger) {
            return $logger;
        });
    }
}
