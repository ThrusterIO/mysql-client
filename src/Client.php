<?php

namespace Thruster\Component\MysqlClient;

use mysqli;
use Thruster\Component\EventLoop\EventLoopInterface;
use Thruster\Component\EventLoop\Timer;
use Thruster\Component\MysqlClient\Exception\ConnectionException;
use Thruster\Component\MysqlClient\Exception\QueryException;
use Thruster\Component\Promise\Deferred;
use Thruster\Component\Promise\ExtendedPromiseInterface;
use Thruster\Component\Promise\FulfilledPromise;
use Thruster\Component\Promise\RejectedPromise;

/**
 * Class Client
 *
 * @package Thruster\Component\MysqlClient
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class Client
{
    /**
     * @var EventLoopInterface
     */
    private $loop;

    /**
     * @var ConnectionPool
     */
    private $connectionPool;

    public function __construct(EventLoopInterface $loop, ConnectionPool $connectionPool)
    {
        $this->loop           = $loop;
        $this->connectionPool = $connectionPool;
    }

    public function query($sql) : ExtendedPromiseInterface
    {
        return $this->connectionPool->getConnection()->then(
            function (mysqli $connection) use ($sql) {
                $status = $connection->query($sql, MYSQLI_ASYNC);

                if (false === $status) {
                    $this->connectionPool->freeConnection($connection);

                    return new RejectedPromise(new QueryException($connection->error, $connection->errno));
                }

                $deferred = new Deferred();

                $this->loop->addPeriodicTimer(0.001, function (Timer $timer) use ($deferred, $connection) {
                    $links = $errors = $reject = [$connection];

                    mysqli_poll($links, $errors, $reject, 0);
                    $read = in_array($connection, $links, true);
                    $error = in_array($connection, $errors, true);
                    $reject = in_array($connection, $reject, true);

                    if ($read || $error || $reject) {
                        if ($read) {
                            $result = $connection->reap_async_query();

                            if (false === $result) {
                                $deferred->reject(new QueryException($connection->error, $connection->errno));
                            } else {
                                $deferred->resolve($result);
                            }
                        } elseif ($error) {
                            $deferred->reject(new QueryException($connection->error, $connection->errno));
                        } else {
                            $deferred->reject(new QueryException('Query was rejected'));
                        }

                        $timer->cancel();
                        $this->connectionPool->freeConnection($connection);
                    }
                });

                return $deferred->promise();
            }
        );
    }

    public function escape($input) : ExtendedPromiseInterface
    {
        return $this->connectionPool->getConnection()->then(
            function (mysqli $connection) use ($input) {
                $result = $connection->real_escape_string($input);

                $this->connectionPool->freeConnection($connection);

                return new FulfilledPromise($result);
            }
        );
    }
}
