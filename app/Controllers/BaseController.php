<?php

declare(strict_types=1);

namespace App\Controllers;

use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Http\Response;
use Swoole\Coroutine;
use Swoole\Http\Request as SwooleRequest;

/**
 * BaseController
 * This is the base controller for all controllers in the application
 *
 */
class BaseController extends Controller
{
    /**
     * @var SwooleRequest swooleRequest
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
        /* 获取Swoole request */
        $this->swooleRequest = Coroutine::getContext()['request'];
        /* 重新定义response */
        $this->response = (new Response())->setStatusCode(200, 'OK');
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

    protected function success(array $data, string $message = ''): Response
    {
        return  $this->response->setContentType('application/json', 'UTF-8')->setJsonContent([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ]);
    }

    protected function error(string $message, string $status = 'error', array $data = []): Response
    {
        return $this->response->setContentType('application/json', 'UTF-8')->setJsonContent([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ]);
    }
}
