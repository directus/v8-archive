<?php

namespace Directus\Database\Schema\Sources\Query;

use Directus\Util\ArrayUtils;
use Zend\Db\Sql\Predicate\In;
use Zend\Db\Sql\Predicate\Like;
use Zend\Db\Sql\Predicate\NotIn;
use Zend\Db\Sql\Predicate\IsNull;
use Zend\Db\Sql\Predicate\Between;
use Zend\Db\Sql\Predicate\NotLike;
use Zend\Db\Sql\Predicate\Operator;
use Directus\Database\Query\Builder;
use Zend\Db\Sql\Predicate\IsNotNull;
use Zend\Db\Sql\Predicate\NotBetween;
use Directus\Database\Schema\Sources\Query\PostgresManyToOneRelation;
use Directus\Database\Schema\Sources\Query\PostgresOneToManyRelation;
use Directus\Database\Schema\Sources\Query\PostgresManyToManyRelation;

/**
 * PostgreSQL-specific SQL builder
 */
class PostgresBuilder extends Builder
{

    public function newQuery()
    {
        return new self($this->connection);
    }

    protected function buildConditionExpression($condition)
    {
        $not = ArrayUtils::get($condition, 'not', false) === true;
        $notChar = '';
        if ($not === true) {
            $notChar = $condition['operator'] === '=' ? '!' : 'n';
        }

        $operator = $notChar . $condition['operator'];

        $column = $condition['column'];
        $identifier = $this->getIdentifier($column);
        $value = $condition['value'];

        if ($value instanceof Builder) {
            $value = $value->buildSelect();
        }

        switch ($operator) {
            case 'in':
                $expression = new In($identifier, $value);
                break;
            case 'nin':
                $expression = new NotIn($identifier, $value);
                break;
            case 'like':
                $sealBreaker = function () {
                    //PostgreSQL is not like MySQL, you first need to cast teh column to do a LIKE comparison
                    $this->specification = '%1$s::TEXT LIKE %2$s';
                    return $this;
                };
                $expression = $sealBreaker->call(new Like($identifier, $value));
                break;
            case 'nlike':
                $sealBreaker = function () {
                    //PostgreSQL is not like MySQL, you first need to cast teh column to do a LIKE comparison
                    $this->specification = '%1$s::TEXT NOT LIKE %2$s';
                    return $this;
                };
                $expression = $sealBreaker->call(new NotLike($identifier, $value));
                break;
            case 'null':
                $expression = new IsNull($identifier);
                break;
            case 'nnull':
                $expression = new IsNotNull($identifier);
                break;
            case 'between':
                $expression = new Between($identifier, array_shift($value), array_pop($value));
                break;
            case 'nbetween':
                $expression = new NotBetween($identifier, array_shift($value), array_pop($value));
                break;
            default:
                $expression = new Operator($identifier, $operator, $value);
        }

        return $expression;
    }

    public function whereAll($column, $table, $columnLeft, $columnRight, $values)
    {
        if ($columnLeft === null) {
            $relation = new PostgresOneToManyRelation($this, $column, $table, $columnRight, $this->getFrom());
        } else {
            $relation = new PostgresManyToManyRelation($this, $table, $columnLeft, $columnRight);
        }

        $relation->all($values);

        return $this->whereIn($column, $relation);
    }

    public function whereHas($column, $table, $columnLeft, $columnRight, $count = 1, $not = false)
    {
        if (is_null($columnLeft)) {
            $relation = new PostgresOneToManyRelation($this, $column, $table, $columnRight, $this->getFrom());
        } else {
            $relation = new PostgresManyToManyRelation($this, $table, $columnLeft, $columnRight);
        }

        // If checking if has 0, this case will be the opposite
        // has = 0, NOT IN the record that has more than 0
        // not has = 0, IN the record that has more than 0
        if ($count < 1) {
            $not = !$not;
        }

        $relation->has($count);

        return $this->whereIn($column, $relation, $not);
    }

    public function whereRelational($column, $table, $columnLeft, $columnRight = null, \Closure $callback = null, $logical = 'and')
    {
        if (is_callable($columnRight)) {
            // $column: Relational Column
            // $table: Related table
            // $columnRight: Related table that points to $column
            $callback = $columnRight;
            $columnRight = $columnLeft;
            $columnLeft = null;
            $relation = new PostgresManyToOneRelation($this, $columnRight, $table);
        } else if (is_null($columnLeft)) {
            $relation = new PostgresOneToManyRelation($this, $column, $table, $columnRight, $this->getFrom());
        } else {
            $relation = new PostgresManyToManyRelation($this, $table, $columnLeft, $columnRight);
        }

        call_user_func($callback, $relation);

        return $this->whereIn($column, $relation, false, $logical);
    }
}
