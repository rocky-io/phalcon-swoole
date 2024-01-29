<?php

declare(strict_types=1);

namespace App\Providers;

use Phalcon\Config\Config;
use Phalcon\Db\Adapter\Pdo;
use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Events\Event;
use Phalcon\Events\Manager;
use RuntimeException;


class DbProvider implements ServiceProviderInterface
{
    /**
     * @var string
     */
    protected $providerName = 'db';

    /**
     * Class map of database adapters, indexed by PDO::ATTR_DRIVER_NAME.
     *
     * @var array
     */
    protected $adapters = [
        'mysql'  => Pdo\Mysql::class,
        'pgsql'  => Pdo\Postgresql::class,
        'sqlite' => Pdo\Sqlite::class,
    ];


    /**
     * @param DiInterface $di
     *
     * @return void
     * @throws RuntimeException
     */
    public function register(DiInterface $di): void
    {
        /** @var Config $config */
        $config = $di->getShared('config')->get('database');
        $class  = $this->getClass($config);
        $config = $this->createConfig($config);
        $logger = $di->getShared('logger');
        $manager = new Manager();
        $manager->attach(
            'db:beforeQuery',
            function (Event $event, $connection) use ($logger) {
                $connection->connect();
                $logger->info(
                    sprintf(
                        '%s - %s',
                        $connection->getSQLStatement(),
                        json_encode($connection->getSQLVariables())
                    )
                );
                return true;
            }
        );
        $db = new $class($config);
        $db->setEventsManager($manager);
        $di->set($this->providerName, function () use ($db) {
            return $db;
        });
    }

    /**
     * Get an adapter class by name.
     *
     * @param Config $config
     *
     * @return string
     * @throws RuntimeException
     */
    private function getClass(Config $config): string
    {
        $name = $config->get('adapter', 'Unknown');

        if (empty($this->adapters[$name])) {
            throw new RuntimeException(
                sprintf(
                    'Adapter "%s" has not been registered',
                    $name
                )
            );
        }

        return $this->adapters[$name];
    }

    private function createConfig(Config $config): array
    {
        // To prevent error: SQLSTATE[08006] [7] invalid connection option "adapter"
        $dbConfig = $config->toArray();
        unset($dbConfig['adapter']);

        $name = $config->get('adapter');
        switch ($this->adapters[$name]) {
            case Pdo\Sqlite::class:
                // Resolve database path
                $dbConfig = ['dbname' => BASE_PATH . "/db/{$config->get('dbname')}.sqlite3"];
                break;
            case Pdo\Postgresql::class:
                // Postgres does not allow the charset to be changed in the DSN.
                unset($dbConfig['charset']);
                break;
        }

        return $dbConfig;
    }
}
