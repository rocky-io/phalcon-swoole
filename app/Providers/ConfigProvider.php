<?php
declare(strict_types=1);

namespace App\Providers;

use Exception;
use Phalcon\Config\Config;
use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;

/**
 * Register the global configuration as config
 */
class ConfigProvider implements ServiceProviderInterface
{
    /**
     * @var string
     */
    protected $providerName = 'config';

    /**
     * @param DiInterface $di
     *
     * @return void
     */
    public function register(DiInterface $di): void
    {
        $configPath = BASE_PATH . '/config/config.php';
        if (!file_exists($configPath) || !is_readable($configPath)) {
            throw new Exception('Config file does not exist: ' . $configPath);
        }

        $di->setShared($this->providerName, function () use ($configPath) {
            $config = include $configPath;

            return new Config($config);
        });
    }
}
