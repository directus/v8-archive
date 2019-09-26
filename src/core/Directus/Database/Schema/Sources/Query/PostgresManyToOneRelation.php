<?php

namespace Directus\Database\Schema\Sources\Query;

use Directus\Database\Schema\Sources\Query\PostgresBuilder;

class PostgresManyToOneRelation extends PostgresBuilder
{
    protected $parentBuilder;
    protected $column;
    protected $columnRight;
    protected $relatedTable;

    public function __construct(PostgresBuilder $builder, $column, $relatedTable)
    {
        parent::__construct($builder->getConnection());

        $this->parentBuilder = $builder;
        $this->column = $column;
        $this->relatedTable = $relatedTable;

        $this->columns([$this->column]);
        $this->from($this->relatedTable);
    }
}
