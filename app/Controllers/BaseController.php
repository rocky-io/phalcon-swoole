<?php

declare(strict_types=1);

namespace App\Controllers;

use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Dispatcher;
use Swoole\Coroutine;
use Swoole\Http\Request;

/**
 * BaseController
 * This is the base controller for all controllers in the application
 *
 */
class BaseController extends Controller
{
    /**
     * @var Request swooleRequest
     */

    protected $swooleRequest;

    /**
     * Executed before every found action
     *
     * @param Dispatcher $dispatcher
     *
     * @return boolean
     */
    private function beforeExecuteRoute(Dispatcher $dispatcher): bool
    {
        $this->swooleRequest = Coroutine::getContext()['request'];
        return true;
    }

    /**
     * Executed after every found action
     *
     * @param Dispatcher $dispatcher
     *
     * @return boolean
     */
    private function afterExecuteRoute(Dispatcher $dispatcher): bool
    {
        return true;
    }

    protected function success($data)
    {
        return  $this->response->setStatusCode(200, 'OK')->setContentType('application/json', 'UTF-8')->setJsonContent([
            'status' => 'success',
            'message' => '',
            'data' => $data
        ]);
    }

    protected function error($message, $status = 'error')
    {
        return $this->response->setStatusCode(200, 'OK')->setContentType('application/json', 'UTF-8')->setJsonContent([
            'status' => $status,
            'message' => $message,
            'data' => ''
        ]);
    }
}
