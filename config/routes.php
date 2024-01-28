<?php

declare(strict_types=1);

use Phalcon\Mvc\Router;
use Phalcon\Mvc\Router\Group;

/**
 * @var $router Router
 */
$router = new Router(false);
$router->removeExtraSlashes(true);
$router->notFound(
    [
        'controller' => 'index',
        'action'     => 'notFound',
    ]
);
$router->addGet(
    '/',
    [
        'controller' => 'index',
        'action'     => 'index',
    ]
);

return $router;
