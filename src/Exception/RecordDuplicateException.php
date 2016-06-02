<?php

namespace Thruster\Component\MysqlClient\Exception;

/**
 * Class RecordDuplicateException
 *
 * @package Thruster\Component\MysqlClient\Exception
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class RecordDuplicateException extends QueryException
{
    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $value;

    public function __construct(string $message)
    {
        preg_match('/Duplicate entry \'([^\']+)\' for key \'([^\']+)\'/', $message, $matches);

        $this->value = $matches[1] ?? '';
        $this->field = $matches[2] ?? '';

        parent::__construct($message, 1062);
    }

    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}
