<?php

namespace Directus\Database\Exception;

use Directus\Exception\ErrorException;
use Zend\Db\Sql\AbstractSql;

class InvalidQueryException extends ErrorException
{
    const ERROR_CODE = 9;

    protected $sql;

    /**
     * UnexpectedValueException constructor.
     *
     * @param AbstractSql $sql
     * @param \Exception|\Throwable $previous
     */
    public function __construct(AbstractSql $sql, $previous = null)
    {
        parent::__construct('Failed generating the SQL query', static::ERROR_CODE, $previous);
    }

    public function getSql()
    {
        return $this->sql;
    }
}
