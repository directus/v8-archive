<?php

namespace Directus\Config;

use Directus\Collection\Collection;
use Directus\Util\ArrayUtils;

class StatusItem
{
    /**
     * @var int
     */
    protected $value;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var int
     */
    protected $sort;

    /**
     * @var array
     */
    protected $attributes;

    /**
     * @var array
     */
    protected $defaultAttributes = [
        'text_color' => '#000000',
        'background_color' => '#FFFFFF',
        'subdued_in_listing' => false,
        'show_listing_badge' => false,
        'hidden_globally' => false,
        'hard_delete' => false,
        'published' => true,
    ];

    public function __construct($value, $name, $sort, array $attributes = [])
    {
        $this->value = $value;
        $this->name = $name;
        $this->sort = $sort;
        $this->attributes = array_merge($this->defaultAttributes, $attributes);
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public function getAttribute($key)
    {
        return ArrayUtils::get($this->attributes, $key);
    }
}
