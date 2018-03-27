<?php

namespace Directus\Database\Ddl\Column;

class Boolean extends \Zend\Db\Sql\Ddl\Column\Column
{
    /**
     * @var int
     */
    protected $length = 1;

    /**
     * @var string
     */
    protected $type = 'TINYINT';

    /**
     * @param null|string     $name
     * @param bool            $nullable
     * @param null|string|int $default
     * @param array           $options
     */
    public function __construct($name, $nullable = false, $default = null, array $options = array())
    {
        $this->setName($name);
        $this->setNullable($nullable);
        $this->setDefault($default);
        $this->setOptions($options);
    }
}
