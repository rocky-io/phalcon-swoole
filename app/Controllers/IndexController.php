<?php

declare(strict_types=1);

namespace App\Controllers;

use Phalcon\Db\Column;

/**
 * Display the default index page.
 */
class IndexController extends BaseController
{
    public function notFoundAction()
    {
        return $this->response->setStatusCode(404, "Not Found")->setContent('The action is not defined');
    }

    public function indexAction()
    {
        $this->logger->info('indexAction');
        $this->success('Hello phalcon-swoole');
    }
}
