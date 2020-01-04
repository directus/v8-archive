<?php

namespace Directus\Collection;

use Directus\Util\ArrayUtils;

class Collection implements CollectionInterface, \Iterator
{
    /**
     * Collection items.
     *
     * @var array
     */
    protected $items = [];

    /**
     * Collection constructor.
     *
     * @param array $items
     */
    public function __construct($items = [])
    {
        $this->items = $items;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return $this->items;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        $this->items[$key] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        return ArrayUtils::get($this->items, $key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        return ArrayUtils::has($this->items, $key);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($key)
    {
        if ($this->has($key)) {
            unset($this->items[$key]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        return 0 === $this->count();
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->items = [];
    }

    /**
     * {@inheritdoc}
     */
    public function replace(array $items)
    {
        $this->clear();
        $this->appendArray($items);
    }

    /**
     * {@inheritdoc}
     */
    public function appendArray(array $items)
    {
        $this->items = array_merge($this->items, $items);

        return $this->items;
    }

    /**
     * {@inheritdoc}
     */
    public function appendCollection(self $collection)
    {
        return $this->appendArray($collection->toArray());
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return \count($this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return current($this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        return next($this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return key($this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return null !== $this->key();
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        return reset($this->items);
    }
}
