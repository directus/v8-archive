<?php

namespace Directus\Config\Schema;

/**
 * Group node.
 */
abstract class Base implements Node
{
    /**
     * Node key.
     *
     * @var string
     */
    private $_key;

    /**
     * Node name.
     *
     * @var string
     */
    private $_name;

    /**
     * Node children.
     *
     * @var Node[]
     */
    private $_children;

    /**
     * Node parent.
     *
     * @var Node
     */
    private $_parent;

    /**
     * Node is optional.
     *
     * @var bool
     */
    private $_optional;

    /**
     * Constructor.
     *
     * @param mixed $name
     * @param mixed $children
     */
    public function __construct($name, $children)
    {
        $this->_optional = '?' === substr($name, -1);
        if ($this->_optional) {
            $name = substr($name, 0, -1);
        }
        $this->_name = $name;
        $this->_key = str_replace('-', '', str_replace('_', '', strtolower($name)));
        $this->_children = $children;
        $this->_parent = null;
        foreach ($children as &$child) {
            $child->parent($this);
        }
    }

    /**
     * Returns the node key.
     *
     * @return string
     */
    public function key()
    {
        return $this->_key;
    }

    /**
     * Returns the node name.
     *
     * @return string
     */
    public function name()
    {
        return $this->_name;
    }

    /**
     * Returns the parent node.
     *
     * @param mixed $value
     *
     * @return Node
     */
    public function parent($value = false)
    {
        if (false !== $value) {
            $this->_parent = $value;
            if (null !== $this->_parent) {
                if (true === $this->_parent->optional()) {
                    $this->_optional = true;
                }
            }
        }

        return $this->_parent;
    }

    /**
     * Returns the children nodes.
     *
     * @param mixed $value
     *
     * @return Node[]
     */
    public function children($value = false)
    {
        if (false !== $value) {
            $this->_children = $value;
        }

        return $this->_children;
    }

    /**
     * Returns wether the node is optional or not.
     *
     * @param null|mixed $value
     *
     * @return bool
     */
    public function optional($value = null)
    {
        if (null !== $value) {
            $this->_optional = $value;
        }

        return $this->_optional;
    }

    /**
     * Returns the $context with normalized array keys.
     *
     * @param $context
     *
     * @return mixed
     */
    protected function normalize($context)
    {
        foreach ($context as $context_key => $context_value) {
            $context[strtolower(str_replace('-', '', str_replace('_', '', $context_key)))] = $context_value;
        }

        return $context;
    }
}
