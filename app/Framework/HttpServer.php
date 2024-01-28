<?php

declare(strict_types=1);

namespace App\Framework;

use Exception;
use Phalcon\Di\FactoryDefault;
use Phalcon\Http\Response as PhalconResponse;
use Phalcon\Mvc\Application;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;
use Swoole\Coroutine;

class HttpServer extends Application
{
    /**
     * Project root path
     *
     * @var string
     */
    protected $rootPath;

    /**
     * @var array $allowedFiles
     */
    protected $allowedFiles;
    /**
     * @var array $httpConfig
     */
    protected $httpConfig;

    /**
     * @var Server $server
     */
    protected $server;
    /**
     * @var FactoryDefault $di
     */
    protected $di;

    public function __construct()
    {
        $this->initialize();
        $this->createServer();
        //$this->di->setShared('app', $this);
        parent::__construct($this->di);
    }

    private function initialize()
    {
        /**
         * disable the view component
         */
        $this->useImplicitView(false);
        /**
         * Init Phalcon Dependency Injection
         */
        $this->di = new FactoryDefault();
        $filename = BASE_PATH . '/config/providers.php';
        if (!file_exists($filename) || !is_readable($filename)) {
            throw new Exception('File providers.php does not exist or is not readable.');
        }
        /** @var array $providers */
        $providers = include_once $filename;
        foreach ($providers as $providerClass) {
            $this->di->register(new $providerClass());
        }
    }

    private function createServer()
    {
        $this->allowedFiles = $this->di->getShared('config')->get('allowedFiles')->toArray();
        $config = $this->di->getShared('config')->get('server')->toArray();
        $this->httpConfig = $config['http'];
        $this->server = new Server($config['http']['ip'], intval($config['http']['port']), $config['mode'], $config['http']['sock_type']);
        $this->server->set($config['http']['settings']);
        if ($config['mode'] == SWOOLE_BASE) {
            $this->server->on('managerStart', [$this, 'onManagerStart']);
        } else {
            $this->server->on('start', [$this, 'onStart']);
        }
        $this->server->on('workerStart', [$this, 'onWorkerStart']);
        $this->server->on('request', [$this, 'onRequest']);
    }

    public function getServer(): Server
    {
        return $this->server;
    }

    public function onManagerStart()
    {
        echo "Swoole Http Server running：http://{$this->httpConfig['ip']}:{$this->httpConfig['port']}" . PHP_EOL;
    }

    public function onStart(Server $server)
    {
        echo "Swoole Http Server running：http://{$this->httpConfig['ip']}:{$this->httpConfig['port']}" . PHP_EOL;
    }

    public function onWorkerStart(Server $server)
    {
    }

    public function onRequest(Request $request, Response $response): bool
    {
        $uri = $request->server['request_uri'];
        // Check static files
        $extension = strtolower(pathinfo($uri, PATHINFO_EXTENSION));
        if (isset($this->allowedFiles[$extension])) {
            if (is_file(PUBLIC_PATH . $uri)) {
                $response->header('Content-Type', $this->allowedFiles[$extension]);
                $response->sendfile(PUBLIC_PATH . $uri);
                return true;
            } else {
                $response->status(404, 'Not Found');
                $response->end();
                return true;
            }
        }
        Coroutine::getContext()['request'] = $request;
        /**
         * @var PhalconResponse $phaResponse
         */
        try {
            $phaResponse = $this->handle($uri);
        } catch (\Throwable $e) {
            $this->di->get('logger')->error(
                'Code: %code%, Message: %message%, Trace: %trace%',
                [
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]
            );

            $appEnv = $this->di->getShared('config')->get('app_env');
            /**
             * @var string $message
             */
            if ($appEnv == 'pro') {
                $message = '系统错误';
            } else {
                $message = $e->getMessage();
            }
            $phaResponse = new PhalconResponse();
            $phaResponse->setStatusCode(500, 'Internal Server Error');
            $phaResponse->setContent($message);
        }
        /**
         * @var HeadersInterface $headers
         */
        $headers = $phaResponse->getHeaders();
        if ($headers) {
            foreach ($headers as $key => $value) {
                $response->header($key, $value);
            }
        }
        /**
         * @var int $statusCode
         */
        $statusCode = $phaResponse->getStatusCode();
        if ($statusCode) {
            $response->status($phaResponse->getStatusCode());
        }
        $response->end($phaResponse->getContent());

        return true;
    }

    /**
     * start http server
     * @return void
     */
    public function start()
    {
        $this->server->start();
    }
}
