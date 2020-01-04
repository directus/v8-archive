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
     * @var null|int
     */
    protected $fieldCount;

    public function __construct($dataSource = null)
    {
        if ($dataSource) {
            $this->initialize($dataSource);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function initialize($dataSource)
    {
        if (\is_array($dataSource)) {
            $first = current($dataSource);
            reset($dataSource);
            $this->fieldCount = \count($first);
            $this->dataSource = new \ArrayIterator($dataSource);
        } else {
            $this->dataSource = $dataSource;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldCount()
    {
        return $this->dataSource->getFieldCount();
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return $this->dataSource->count();
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return new ResultItem($this->dataSource->current());
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        return $this->dataSource->next();
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->dataSource->key();
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return $this->dataSource->valid();
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function buffer()
    {
        return $this->dataSource->buffer();
    }

    /**
     * {@inheritdoc}
     */
    public function isBuffered()
    {
        return $this->dataSource->isBuffered();
    }

    /**
     * {@inheritdoc}
     */
    public function isQueryResult()
    {
        return $this->dataSource->isQueryResult();
    }

    /**
     * {@inheritdoc}
     */
    public function getAffectedRows()
    {
        return $this->dataSource->getAffectedRows();
    }

    /**
     * {@inheritdoc}
     */
    public function getGeneratedValue()
    {
        return $this->dataSource->getGeneratedValue();
    }

    /**
     * {@inheritdoc}
     */
    public function getResource()
    {
        return $this->dataSource->getResource();
    }
}
