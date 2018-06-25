<?php

namespace Directus\Database\Ddl\Column;

use Zend\Db\Sql\Ddl\Column\AbstractLengthColumn;

class Real extends AbstractLengthColumn
{
    /**
     * @var string
     */
    protected $type = 'REAL';
}
