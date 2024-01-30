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

$article = new Group(
    [
        'controller' => 'article',
    ]
);
$article->setPrefix('/article');
$article->addGet(
    '/view/{id:[0-9]+}',
    [
        'action' => 'view'
    ]
);
$article->addPost(
    '/create',
    [
        'action' => 'create'
    ]
);
$article->addPost(
    '/update/{id:[0-9]+}',
    [
        'action' => 'update'
    ]
);
$article->addPost(
    '/delete/{id:[0-9]+}',
    [
        'action' => 'delete'
    ]
);
$article->addGet(
    '/list',
    [
        'action' => 'list'
    ]
);
$router->mount($article);
return $router;
