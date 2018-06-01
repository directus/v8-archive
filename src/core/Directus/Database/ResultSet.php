<?php

namespace Directus\Database;

use Directus\Exception\Exception;
use Symfony\Component\Validator\Tests\Fixtures\Countable;
use Zend\Db\ResultSet\ResultSetInterface;
use Zend\Db\Adapter\Driver\ResultInterface;

class ResultSet implements \Iterator, ResultSetInterface
{
    /**
     * @var \ArrayIterator|ResultSetInterface
     */
    protected $dataSource;

    /**
     * @var array
     */
    protected $buffer = [];

    /**
     * @var null|int
     */
    protected $count = null;

    /**
     * @var null|int
     */
    protected $fieldCount = null;

    /**
     * @var int
     */
    protected $position = 0;

    protected $fetched = 0;

    public function __construct($dataSource = null)
    {
        if ($dataSource) {
            $this->initialize($dataSource);
        }
    }

    /**
     * @inheritDoc
     */
    public function initialize($dataSource)
    {
        if ($dataSource instanceof ResultInterface) {
            $this->dataSource = $dataSource;
        } else if (is_array($dataSource)) {
            $first = current($dataSource);
            reset($dataSource);
            $this->fieldCount = count($first);
            $this->dataSource = new \ArrayIterator($dataSource);
        } else {
            throw new Exception(
                sprintf('Data Source must be an array or an instance of ResultInterface. "%s" given', gettype($dataSource))
            );
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFieldCount()
    {
        if ($this->fieldCount === null) {
            $this->fieldCount = $this->dataSource->getFieldCount();
        }

        return $this->fieldCount;
    }

    /**
     * @inheritDoc
     */
    public function count()
    {
        if ($this->count === null) {
            if ($this->dataSource instanceof Countable) {
                $this->count = count($this->dataSource);
            }
        }

        return $this->count;
    }

    /**
     * @inheritDoc
     */
    public function current()
    {
        if (!isset($this->buffer[$this->position])) {
            $this->fetched++;
            $this->buffer[$this->position] = new ResultItem($this->dataSource->current());
        }

        return $this->buffer[$this->position];
    }

    /**
     * @inheritDoc
     */
    public function next()
    {
        $this->position++;
        $this->dataSource->next();
    }

    /**
     * @inheritDoc
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * @inheritDoc
     */
    public function valid()
    {
        if (isset($this->buffer[$this->position])) {
            return true;
        }

        return $this->dataSource->valid();
    }

    /**
     * @inheritDoc
     */
    public function rewind()
    {
        $this->position = 0;
        reset($this->buffer);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        if ($this->fetched > 0) {
            $items = [];
            /** @var ResultItem $item */
            foreach ($this->buffer as $item) {
                $items[] = $item->toArray();
            }

            return $items;
        }

        return iterator_to_array($this->dataSource);
    }
}
