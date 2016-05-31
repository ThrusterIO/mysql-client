<?php

namespace Thruster\Component\MysqlClient\Exception;

use Exception;

/**
 * Class ConnectionException
 *
 * @package Thruster\Component\MysqlClient\Exception
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class ConnectionException extends Exception
{
    public function __construct()
    {
        parent::__construct(mysqli_connect_error(), mysqli_connect_errno());
    }
}
