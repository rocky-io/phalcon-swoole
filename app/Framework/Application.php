<?php

declare(strict_types=1);

namespace App\Framework;

class Application
{
    static $command;

    private static function welcome()
    {
        $displayItem['php version'] = phpversion();
        $displayItem['swoole version'] = SWOOLE_VERSION;
        $displayItem['phalcon version'] = (new \Phalcon\Support\Version())->get();
        $msg = '';
        foreach ($displayItem as $key => $value) {
            $msg .= self::displayItem($key, $value) . PHP_EOL;
        }
        echo $msg;
    }

    private static function displayItem($name, $value)
    {
        if ($value === true) {
            $value = 'true';
        } else if ($value === false) {
            $value = 'false';
        } else if ($value === null) {
            $value = 'null';
        } else if (is_array($value)) {
            $value = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
        return "\e[1;32m" . str_pad($name, 30, ' ', STR_PAD_RIGHT) . "\e[34m" . $value . "\e[0m";
    }

    public static function run()
    {
        self::welcome();
        global $argv;
        $count = count($argv);
        $command = $argv[$count - 1];
        self::$command = $command;
        switch ($command) {
            case 'start':
                self::start();
                break;
            case 'stop':
                self::stop();
                break;
            case 'restart':
                self::restart();
                break;
            case 'reload':
                self::reload();
                break;
            default:
                exit(Color::danger("use {$argv[0]} [start] [stop] [restart] [reload]" . PHP_EOL));
        }
    }

    private static function start()
    {
        (new HttpServer())->start();
    }

    private static function stop()
    {
        $configPath = BASE_PATH . '/config/config.php';
        if (!file_exists($configPath) || !is_readable($configPath)) {
            throw new \Exception('Config file does not exist: ' . $configPath);
        }
        $config = include $configPath;
        $serverConfig = $config['server']['http'];
        $pidFile = $serverConfig['settings']['pid_file'];
        if (file_exists($pidFile)) {
            $pid = intval(file_get_contents($pidFile));
            if (!\Swoole\Process::kill($pid, 0)) {
                echo Color::danger("pid :{$pid} not exist " . PHP_EOL);
                unlink($pidFile);
            } else {
                \Swoole\Process::kill($pid, SIGKILL);
                //等待5秒
                $time = time();
                while (true) {
                    usleep(1000);
                    if (!\Swoole\Process::kill($pid, 0)) {
                        if (is_file($pidFile)) {
                            unlink($pidFile);
                        }
                        $msg = "server stop for pid {$pid} at " . date("Y-m-d H:i:s") . PHP_EOL;
                        echo Color::success($msg);
                        break;
                    } else {
                        if (time() - $time > 5) {
                            echo Color::danger("stop server fail for pid:{$pid} , please try again" . PHP_EOL);
                            break;
                        }
                    }
                }
            }
        } else {
            echo Color::danger("pid file does not exist !" . PHP_EOL);
        }
    }

    private static function reload()
    {
        $configPath = BASE_PATH . '/config/config.php';
        if (!file_exists($configPath) || !is_readable($configPath)) {
            throw new \Exception('Config file does not exist: ' . $configPath);
        }
        $config = include $configPath;
        $serverConfig = $config['server']['http'];
        $pidFile = $serverConfig['settings']['pid_file'];
        if (file_exists($pidFile)) {
            //清除opcache缓存
            if (function_exists('opcache_reset')) {
                opcache_reset();
            }
            $pid = intval(file_get_contents($pidFile));
            if (!\Swoole\Process::kill($pid, 0)) {
                echo Color::danger("pid :{$pid} not exist " . PHP_EOL);
            } else {
                \Swoole\Process::kill($pid, SIGUSR1);
                $msg = "send server reload command to pid:{$pid} at " . date("Y-m-d H:i:s") . PHP_EOL;
                echo Color::success($msg);
            }
        } else {
            echo Color::danger("pid file does not exist !" . PHP_EOL);
        }
    }

    private static function restart()
    {
        self::stop();
        self::start();
    }
}
