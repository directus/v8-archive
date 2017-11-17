<?php

namespace Directus\Authentication\User;

use Directus\Util\ArrayUtils;

class User implements UserInterface
{
    /**
     * User attributes
     *
     * @var array
     */
    protected $attributes;

    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    /**
     * Gets the attribute with the given name
     *
     * @param $name
     *
     * @return mixed
     */
    public function get($name)
    {
        return ArrayUtils::get($this->attributes, $name);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->get('id');
    }

    /**
     * @inheritdoc
     */
    public function getEmail()
    {
        return $this->get('email');
    }

    /**
     * Access the attribute as property
     *
     * @param $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        if (!array_key_exists($name, $this->attributes)) {
            throw new \RuntimeException(sprintf('Property "%s" does not exists.', $name));
        }

        // TODO: Omit sensitive data
        return $this->attributes[$name];
    }

    /**
     * @inheritdoc
     */
    public function toArray()
    {
        return $this->attributes;
    }
}
