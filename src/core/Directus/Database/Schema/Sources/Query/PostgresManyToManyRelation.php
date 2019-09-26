<?php

namespace Directus\Database\Schema\Sources\Query;

use Directus\Database\Schema\Sources\Query\PostgresBuilder;
use Zend\Db\Sql\Predicate\Expression;

class PostgresManyToManyRelation extends PostgresBuilder
{
    protected $parentBuilder;
    protected $table;
    protected $columnLeft;
    protected $columnRight;

    public function __construct(PostgresBuilder $builder, $table, $columnLeft, $columnRight)
    {
        parent::__construct($builder->getConnection());

        $this->parentBuilder = $builder;
        $this->table = $table;
        $this->columnLeft = $columnLeft;
        $this->columnRight = $columnRight;

        $this->from($table);
    }

    public function all(array $values)
    {
        $this->columns([$this->columnLeft]);
        $this->whereIn($this->columnRight, $values);
        $this->groupBy($this->columnLeft);
        $this->having(new Expression('COUNT(*) = ?', count($values)));

        return $this;
    }

    public function has($count = 1)
    {
        $this->columns([$this->columnLeft]);
        $this->groupBy($this->columnLeft);
        $this->having(new Expression('COUNT(*) >= ?', (int) $count));

        return $this;
    }
}