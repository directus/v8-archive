<?php

namespace Directus\Database\Ddl\Column;

use Zend\Db\Sql\Ddl\Column\AbstractLengthColumn;

class Double extends AbstractLengthColumn
{
    /**
     * @var string
     */
    protected $type = 'DOUBLE';
}
