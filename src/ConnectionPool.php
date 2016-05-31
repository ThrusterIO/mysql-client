<?php

namespace Thruster\Component\MysqlClient;

use mysqli;
use SplObjectStorage;
use Thruster\Component\MysqlClient\Exception\ConnectionException;
use Thruster\Component\Promise\Deferred;
use Thruster\Component\Promise\ExtendedPromiseInterface;
use Thruster\Component\Promise\FulfilledPromise;
use Thruster\Component\Promise\RejectedPromise;

/**
 * Class ConnectionPool
 *
 * @package Thruster\Component\MysqlClient
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class ConnectionPool
{
    /**
     * @var callable
     */
    private $connectionFactory;

    /**
     * @var SplObjectStorage
     */
    private $connectionPool;

    /**
     * @var SplObjectStorage
     */
    private $idleConnections;

    /**
     * @var Deferred[]
     */
    private $waiting;

    /**
     * @var int
     */
    private $maxOpenConnections;

    public function __construct(callable $connectionFactory, int $maxOpenConnections = 100)
    {
        $this->connectionPool  = new SplObjectStorage();
        $this->idleConnections = new SplObjectStorage();

        $this->waiting            = [];
        $this->maxOpenConnections = $maxOpenConnections;
        $this->connectionFactory  = $connectionFactory;
    }

    public function getConnection() : ExtendedPromiseInterface
    {
        if ($this->idleConnections->count() > 0) {
            $this->idleConnections->rewind();

            $connection = $this->idleConnections->current();
            $this->idleConnections->detach($connection);

            return new FulfilledPromise($connection);
        }

        if ($this->connectionPool->count() >= $this->maxOpenConnections) {
            $deferred = new Deferred();

            $this->waiting[] = $deferred;

            return $deferred->promise();
        }

        $connection = call_user_func($this->connectionFactory);

        if (false !== $connection) {
            $this->connectionPool->attach($connection);

            return new FulfilledPromise($connection);
        } else {
            return new RejectedPromise(new ConnectionException());
        }
    }

    public function freeConnection(mysqli $connection)
    {
        if (count($this->waiting) > 0) {
            $deferred = array_shift($this->waiting);
            $deferred->resolve($connection);
        } else {
            $this->idleConnections->attach($connection);
        }
    }
}
