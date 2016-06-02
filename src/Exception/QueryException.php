<?php

namespace Thruster\Component\MysqlClient\Exception;

use Exception;

/**
 * Class QueryException
 *
 * @package Thruster\Component\MysqlClient\Exception
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class QueryException extends Exception
{
    public static function create(string $message, int $code)
    {
        switch ($code) {
            case 1062:
                return new RecordDuplicateException($message);
                break;

            default:
                return new static($message, $code);
        }
    }
}
