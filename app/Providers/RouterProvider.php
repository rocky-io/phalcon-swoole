<?php
declare(strict_types=1);

namespace App\Providers;

use Exception;
use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Mvc\Router;

class RouterProvider implements ServiceProviderInterface
{
    /**
     * @var string
     */
    protected $providerName = 'router';

    /**
     * @param DiInterface $di
     *
     * @return void
     */
    public function register(DiInterface $di): void
    {
        $di->set($this->providerName, function () {
            $routePath = BASE_PATH . '/config/routes.php';
            if (!file_exists($routePath) || !is_readable($routePath)) {
                throw new Exception($routePath . ' file does not exist or is not readable.');
            }
            $router = require_once $routePath;
            return $router;
        });
    }
}
