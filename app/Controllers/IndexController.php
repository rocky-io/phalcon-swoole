<?php

declare(strict_types=1);

namespace App\Controllers;

use Phalcon\Http\Response;

/**
 * Display the default index page.
 */
class IndexController extends BaseController
{
    public function notFoundAction(): Response
    {
        return $this->response->setStatusCode(404, "Not Found")->setContent('The action is not defined');
    }

    public function indexAction(): Response
    {
        $this->logger->info('indexAction');
        return $this->success([], 'Hello phalcon-swoole');
    }
}
