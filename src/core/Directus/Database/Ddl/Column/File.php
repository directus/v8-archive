<?php

namespace Directus\Database\Ddl\Column;

use Zend\Db\Sql\Ddl\Column\Integer;

class File extends Integer
{
    /**
     * @var string
     */
    protected $type = 'INT';
}
