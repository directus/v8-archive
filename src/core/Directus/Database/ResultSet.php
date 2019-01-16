<?php

namespace Directus\Database;

use Zend\Db\Adapter\Driver\Pdo\Result;
use Zend\Db\Adapter\Driver\ResultInterface;

class ResultSet implements \Iterator, ResultInterface
{
    /**
     * @var Result
     */
    protected $dataSource;

    /**
     * @var int|null
     */
    protected $fieldCount = null;

    /**
     * @var int|null
     */
    protected $foundRows = null;

    public function __construct($dataSource = null, $foundRows = null)
    {
        if ($dataSource) {
            $this->initialize($dataSource, $foundRows);
        }
    }

    /**
     * @inheritDoc
     */
    public function initialize($dataSource, $foundRows = null)
    {
        if (is_array($dataSource)) {
            $first = current($dataSource);
            reset($dataSource);
            $this->fieldCount = count($first);
            $this->dataSource = new \ArrayIterator($dataSource);
        } else {
            $this->dataSource = $dataSource;
        }

        $this->foundRows = $foundRows;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFieldCount()
    {
        return $this->dataSource->getFieldCount();
    }

    /**
     * @return int|null
     */
    public function getFoundRows()
    {
        return $this->foundRows;
    }

    /**
     * @inheritDoc
     */
    public function count()
    {
        return $this->dataSource->count();
    }

    /**
     * @inheritDoc
     */
    public function current()
    {
        return new ResultItem($this->dataSource->current());
    }

    /**
     * @inheritDoc
     */
    public function next()
    {
        return $this->dataSource->next();
    }

    /**
     * @inheritDoc
     */
    public function key()
    {
        return $this->dataSource->key();
    }

    /**
     * @inheritDoc
     */
    public function valid()
    {
        return $this->dataSource->valid();
    }

    /**
     * @inheritDoc
     */
    public function rewind()
    {
        $this->dataSource->rewind();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return iterator_to_array($this->dataSource);
    }

    /**
     * @inheritDoc
     */
    public function buffer()
    {
        return $this->dataSource->buffer();
    }

    /**
     * @inheritDoc
     */
    public function isBuffered()
    {
        return $this->dataSource->isBuffered();
    }

    /**
     * @inheritDoc
     */
    public function isQueryResult()
    {
        return $this->dataSource->isQueryResult();
    }

    /**
     * @inheritDoc
     */
    public function getAffectedRows()
    {
        return $this->dataSource->getAffectedRows();
    }

    /**
     * @inheritDoc
     */
    public function getGeneratedValue()
    {
        return $this->dataSource->getGeneratedValue();
    }

    /**
     * @inheritDoc
     */
    public function getResource()
    {
        return $this->dataSource->getResource();
    }
}
